// Le rendu JavaScript de l'outil de conformité, éprouvé avec un vrai Chrome.
//
// Une page de test se construit par JavaScript, puis tente de joindre un
// second serveur, « interne », par fetch, par son nom localhost et par
// WebSocket. Le rendu doit voir le contenu construit, et le serveur interne
// ne doit recevoir aucune requête : c'est la garde du mandataire.
//
// Sans Chrome ni puppeteer-core installés (outils/rendu), l'essai est
// ignoré : le rendu est une option du serveur, pas une dépendance.

const fs = require("fs");
const http = require("http");
const path = require("path");
const { spawn } = require("child_process");

const RACINE = path.join(__dirname, "..");
const CHROMES = [
  process.env.CONFORMITE_CHROME,
  "C:/Program Files/Google/Chrome/Application/chrome.exe",
  "C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe",
  "/usr/bin/chromium", "/usr/bin/chromium-browser", "/usr/bin/google-chrome",
].filter(Boolean);
const chrome = CHROMES.find((c) => fs.existsSync(c));

if (!chrome || !fs.existsSync(path.join(RACINE, "outils", "rendu", "node_modules", "puppeteer-core"))) {
  console.log("  ignoré : Chrome ou puppeteer-core absent (npm install dans outils/rendu)");
  process.exit(0);
}

let ko = 0;
function verifier(libelle, condition, detail) {
  if (!condition) ko++;
  console.log("  " + (condition ? "ok  " : "ECHEC ") + libelle + (condition || !detail ? "" : "  (" + detail + ")"));
}

/* Asynchrone : les serveurs de l'essai tournent dans ce même processus,
   un appel bloquant les empêcherait de répondre au navigateur. */
function rendre(demande) {
  return new Promise((resoudre) => {
    const enfant = spawn(process.execPath, [path.join(RACINE, "outils", "rendu", "rendu.js")]);
    let sortie = "";
    let erreurs = "";
    const minuterie = setTimeout(() => enfant.kill(), 120000);
    enfant.stdout.on("data", (d) => { sortie += d; });
    enfant.stderr.on("data", (d) => { erreurs += d; });
    enfant.on("close", () => {
      clearTimeout(minuterie);
      try { resoudre(JSON.parse(sortie)); } catch (e) { resoudre({ erreur: "sortie illisible : " + (erreurs || sortie).slice(0, 200) }); }
    });
    enfant.stdin.end(JSON.stringify(Object.assign({ chrome: chrome, pagesMax: 2, delaiPage: 20, dureeMax: 60 }, demande)));
  });
}

const appelsInternes = [];
const interne = http.createServer((req, res) => { appelsInternes.push(req.url); res.end("secret"); });
interne.on("upgrade", (req, socket) => { appelsInternes.push("ws " + req.url); socket.destroy(); });

const site = http.createServer((req, res) => {
  const portInterne = interne.address().port;
  res.setHeader("Content-Type", "text/html; charset=utf-8");
  res.end(`<!doctype html><html lang="fr"><head><title>Essai</title></head><body>
    <div id="app"></div>
    <script>
      document.getElementById("app").innerHTML =
        '<h1>Construit par JavaScript</h1><p style="color:#c8c8c8;background:#fff">Texte trop pâle</p>';
      fetch("http://127.0.0.1:${portInterne}/secret").catch(function () {});
      fetch("http://localhost:${portInterne}/par-nom").catch(function () {});
      try { new WebSocket("ws://127.0.0.1:${portInterne}/ws"); } catch (e) {}
    </script></body></html>`);
});

interne.listen(0, "127.0.0.1", () => site.listen(0, "127.0.0.1", async () => {
  const portSite = site.address().port;
  const sortie = await rendre({ adresse: "http://127.0.0.1:" + portSite + "/", autoriser: ["127.0.0.1:" + portSite] });

  verifier("le rendu aboutit", !sortie.erreur, sortie.erreur);
  const html = sortie.pages && sortie.pages[0] ? sortie.pages[0].html : "";
  verifier("le contenu construit par JavaScript est vu", html.indexOf("Construit par JavaScript") >= 0);

  /* Les requêtes de la page partent pendant le rendu : on laisse au
     serveur interne le temps d'en recevoir une, s'il devait y en avoir. */
  setTimeout(async () => {
    verifier("le serveur interne n'a reçu aucune requête", appelsInternes.length === 0, appelsInternes.join(", "));
    verifier("les tentatives sont consignées comme refusées",
      (sortie.refus || []).some((r) => r.indexOf(String(interne.address().port)) >= 0), JSON.stringify(sortie.refus));
    const ecarts = sortie.contrastes ? sortie.contrastes.ecarts : [];
    verifier("le contraste trop faible est mesuré", ecarts.some((e) => e.texte.indexOf("pâle") >= 0), JSON.stringify(ecarts));

    const prive = await rendre({ adresse: "http://127.0.0.1:" + portSite + "/" });
    verifier("une adresse interne est refusée sans autorisation", /réseau interne|port/.test(prive.erreur || ""), prive.erreur);

    site.close();
    interne.close();
    console.log();
    console.log(ko ? "  " + ko + " echec(s)" : "  Le rendu est isolé du réseau interne.");
    process.exit(ko ? 1 : 0);
  }, 500);
}));
