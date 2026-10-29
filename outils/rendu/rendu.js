#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Rendu des pages par un navigateur sans interface
 *
 *   node outils/rendu/rendu.js < demande.json > rendu.json
 *
 * Employé par l'outil de contrôle de conformité pour les sites rendus en
 * JavaScript (React, Angular…) : Chrome ouvre la page, exécute son code
 * comme chez l'usager, et le script rend le HTML final, les feuilles et
 * scripts chargés, le poids réel et les contrastes affichés.
 *
 * Le code exécuté est celui d'un tiers. Tout ce que Chrome demande au
 * réseau passe donc par un mandataire local, ouvert par ce script, qui
 * applique la garde de l'outil : http et https seulement, aucune adresse
 * du réseau interne, résolution du nom faite ici puis connexion sur
 * l'adresse vérifiée. Les WebSocket y passent aussi, et WebRTC est coupé.
 *
 * Demande (JSON sur l'entrée standard) :
 *   adresse        page de départ
 *   chrome         chemin de l'exécutable de Chrome ou Chromium
 *   pagesMax       pages du même domaine à rendre (20)
 *   delaiPage      secondes par page (30)
 *   dureeMax       secondes pour toute l'analyse (180)
 *   autoriser      liste « hôte:port » admis malgré la garde (essais seulement)
 */
"use strict";

const dns = require("dns").promises;
const fs = require("fs");
const http = require("http");
const net = require("net");
const os = require("os");
const path = require("path");

const OCTETS_MAX = 2 * 1024 * 1024;      // par réponse conservée
const TOTAL_MAX = 60 * 1024 * 1024;      // transfert de toute l'analyse
const RESSOURCES_MAX = 60;

/* ----------------------------------------------------------- garde */

function privee(ip) {
  if (net.isIPv4(ip)) {
    const [a, b] = ip.split(".").map(Number);
    return a === 0 || a === 10 || a === 127 || a >= 224
      || (a === 100 && b >= 64 && b <= 127)
      || (a === 169 && b === 254)
      || (a === 172 && b >= 16 && b <= 31)
      || (a === 192 && b === 168)
      || (a === 192 && b === 0)
      || (a === 198 && (b === 18 || b === 19));
  }
  const v = ip.toLowerCase();
  if (v.startsWith("::ffff:")) return privee(v.slice(7));
  return v === "::" || v === "::1" || v.startsWith("fc") || v.startsWith("fd") || v.startsWith("fe80");
}

/** Résout un hôte et rend l'adresse à joindre, ou lève une erreur. */
async function verifier(hote, port, autoriser) {
  hote = hote.replace(/^\[|\]$/g, "").toLowerCase();
  if (autoriser.has(hote + ":" + port)) return hote;
  if (port !== 80 && port !== 443) throw new Error("port " + port + " refusé");

  const ips = net.isIP(hote) ? [hote] : (await dns.lookup(hote, { all: true })).map((r) => r.address);
  if (!ips.length) throw new Error(hote + " sans adresse");
  for (const ip of ips) {
    if (privee(ip)) throw new Error(hote + " mène au réseau interne");
  }
  return ips[0];
}

/* ---------------------------------------- le mandataire de filtrage */

function mandataire(autoriser, refus, compteur) {
  const serveur = http.createServer(async (req, res) => {
    /* Requête http ordinaire : l'adresse complète arrive dans la ligne. */
    let cible;
    try {
      cible = new URL(req.url);
      if (cible.protocol !== "http:") throw new Error("protocole refusé");
      const port = Number(cible.port || 80);
      const ip = await verifier(cible.hostname, port, autoriser);
      const amont = http.request({
        host: ip, port, method: req.method, path: cible.pathname + cible.search,
        headers: Object.assign({}, req.headers, { host: cible.host }),
        timeout: 15000,
      }, (reponse) => {
        res.writeHead(reponse.statusCode, reponse.headers);
        reponse.on("data", (morceau) => {
          compteur.octets += morceau.length;
          if (compteur.octets > TOTAL_MAX) { reponse.destroy(); res.destroy(); }
        });
        reponse.pipe(res);
      });
      amont.on("error", () => { if (!res.headersSent) res.writeHead(502); res.end(); });
      amont.on("timeout", () => amont.destroy());
      req.pipe(amont);
    } catch (e) {
      refus.push((cible ? cible.href : req.url) + " : " + e.message);
      res.writeHead(403);
      res.end();
    }
  });

  /* https et wss : un tunnel CONNECT vers hôte:port, vérifié de même. */
  serveur.on("connect", async (req, client, tete) => {
    const [hote, portTexte] = req.url.split(/:(?=\d+$)/);
    const port = Number(portTexte || 443);
    try {
      const ip = await verifier(hote, port, autoriser);
      const amont = net.connect(port, ip, () => {
        client.write("HTTP/1.1 200 Connection Established\r\n\r\n");
        amont.write(tete);
        amont.pipe(client);
        client.pipe(amont);
      });
      amont.on("data", (morceau) => {
        compteur.octets += morceau.length;
        if (compteur.octets > TOTAL_MAX) { amont.destroy(); client.destroy(); }
      });
      amont.setTimeout(20000, () => amont.destroy());
      amont.on("error", () => client.destroy());
      client.on("error", () => amont.destroy());
    } catch (e) {
      refus.push(req.url + " : " + e.message);
      client.end("HTTP/1.1 403 Forbidden\r\n\r\n");
    }
  });

  return new Promise((resoudre) => serveur.listen(0, "127.0.0.1", () => resoudre(serveur)));
}

/* ----------------------------------------- mesure dans la page rendue */

/* Exécuté dans la page. Pour chaque élément qui porte du texte, on compose
   sa couleur sur le premier fond opaque de ses ancêtres. Un fond en image
   ou en dégradé rend la mesure impossible : l'élément est compté à part. */
function mesurerContrastes() {
  function rgba(texte) {
    const m = texte.match(/rgba?\(([^)]+)\)/);
    if (!m) return null;
    const p = m[1].split(/[\s,\/]+/).filter(Boolean).map(Number);
    return [p[0], p[1], p[2], p.length > 3 ? p[3] : 1];
  }
  function lin(c) { c /= 255; return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4); }
  function lum(c) { return 0.2126 * lin(c[0]) + 0.7152 * lin(c[1]) + 0.0722 * lin(c[2]); }
  function composer(haut, bas) {
    const a = haut[3];
    return [0, 1, 2].map((i) => haut[i] * a + bas[i] * (1 - a)).concat(1);
  }
  function fond(el) {
    const couches = [];
    for (let n = el; n && n.nodeType === 1; n = n.parentElement) {
      const s = getComputedStyle(n);
      if (s.backgroundImage && s.backgroundImage !== "none") return null;
      const c = rgba(s.backgroundColor);
      if (c && c[3] > 0) { couches.push(c); if (c[3] >= 1) break; }
    }
    let resultat = [255, 255, 255, 1];
    for (let i = couches.length - 1; i >= 0; i--) resultat = composer(couches[i], resultat);
    return resultat;
  }
  function chemin(el) {
    const parties = [];
    for (let n = el; n && n.nodeType === 1 && parties.length < 4; n = n.parentElement) {
      let p = n.tagName.toLowerCase();
      if (n.id) { parties.unshift(p + "#" + n.id); break; }
      const classe = (n.getAttribute("class") || "").trim().split(/\s+/)[0];
      if (classe) p += "." + classe;
      parties.unshift(p);
    }
    return parties.join(" > ");
  }

  const ecarts = [];
  let mesures = 0;
  let impossibles = 0;
  const vus = new Set();

  for (const el of document.body ? document.body.querySelectorAll("*") : []) {
    const texte = Array.from(el.childNodes).filter((n) => n.nodeType === 3).map((n) => n.textContent).join("").trim();
    if (!texte) continue;
    const s = getComputedStyle(el);
    if (s.visibility === "hidden" || s.display === "none" || parseFloat(s.opacity) === 0) continue;
    const r = el.getBoundingClientRect();
    /* Un texte réservé aux lecteurs d'écran (1 px de côté) ou un décor
       masqué par aria-hidden ne se lit pas à l'œil : il n'est pas mesuré. */
    if (r.width < 2 || r.height < 2 || el.closest("[aria-hidden=\"true\"]")) continue;

    const couleur = rgba(s.color);
    const f = fond(el);
    if (!couleur || !f) { impossibles++; continue; }
    mesures++;

    const c = composer(couleur, f);
    const l1 = lum(c), l2 = lum(f);
    const rapport = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
    const taille = parseFloat(s.fontSize);
    const gras = parseInt(s.fontWeight, 10) >= 700;
    const grand = taille >= 24 || (gras && taille >= 18.66);
    const seuil = grand ? 3 : 4.5;

    if (rapport < seuil) {
      const cle = chemin(el) + "|" + Math.round(rapport * 100);
      if (vus.has(cle)) continue;
      vus.add(cle);
      ecarts.push({
        element: chemin(el),
        texte: texte.slice(0, 60),
        rapport: Math.round(rapport * 100) / 100,
        seuil: seuil,
      });
    }
  }
  return { ecarts: ecarts.slice(0, 50), mesures: mesures, impossibles: impossibles };
}

/* ------------------------------------------------------------ rendu */

async function principal() {
  const demande = JSON.parse(fs.readFileSync(0, "utf8"));
  const pagesMax = demande.pagesMax || 20;
  const delai = (demande.delaiPage || 30) * 1000;
  const echeance = Date.now() + (demande.dureeMax || 180) * 1000;
  const autoriser = new Set((demande.autoriser || []).map((x) => x.toLowerCase()));

  const depart = new URL(demande.adresse);
  if (!/^https?:$/.test(depart.protocol)) throw new Error("Seules les adresses http et https sont acceptées.");
  await verifier(depart.hostname, Number(depart.port || (depart.protocol === "https:" ? 443 : 80)), autoriser);

  const refus = [];
  const compteur = { octets: 0 };
  const proxy = await mandataire(autoriser, refus, compteur);
  const profil = fs.mkdtempSync(path.join(os.tmpdir(), "charte-rendu-"));

  const puppeteer = require("puppeteer-core");
  const args = [
    "--proxy-server=http://127.0.0.1:" + proxy.address().port,
    /* Sans cette règle, Chrome contournerait le mandataire pour localhost. */
    "--proxy-bypass-list=<-loopback>",
    "--force-webrtc-ip-handling-policy=disable_non_proxied_udp",
    "--disable-features=WebRtcHideLocalIpsWithMdns,DnsOverHttps",
    "--dns-prefetch-disable",
    "--disable-background-networking",
    "--disable-extensions",
    "--disable-sync",
    "--no-first-run",
    "--mute-audio",
    "--disable-gpu",
  ];
  /* Le bac à sable de Chrome ne démarre pas sous root : mieux vaut lancer
     l'outil sous un compte ordinaire, mais on ne bloque pas l'analyse. */
  if (typeof process.getuid === "function" && process.getuid() === 0) args.push("--no-sandbox");

  const navigateur = await puppeteer.launch({
    executablePath: demande.chrome,
    headless: true,
    userDataDir: profil,
    args: args,
  });

  const sortie = { pages: [], ressources: [], poidsPremierePage: null, contrastes: null, nonAtteints: [], refus: refus };
  const aVisiter = [depart.href];
  const vues = new Set(aVisiter);
  const ressourcesVues = new Set();

  try {
    while (aVisiter.length && sortie.pages.length < pagesMax) {
      const adresse = aVisiter.shift();
      if (Date.now() > echeance) { sortie.nonAtteints.push(adresse + " : durée d'analyse épuisée"); continue; }

      const contexte = await navigateur.createBrowserContext();
      const page = await contexte.newPage();
      await page.setUserAgent((await navigateur.userAgent()) + " CharteGraphiqueBF-Controle/3.0");
      await page.setViewport({ width: 1280, height: 900 });

      let poids = 0;
      const premiere = sortie.pages.length === 0;
      page.on("response", async (reponse) => {
        try {
          const type = reponse.request().resourceType();
          const corps = await reponse.buffer().catch(() => null);
          if (!corps) return;
          poids += corps.length;
          const url = reponse.url();
          if ((type === "stylesheet" || type === "script") && !ressourcesVues.has(url)
              && ressourcesVues.size < RESSOURCES_MAX && corps.length <= OCTETS_MAX) {
            ressourcesVues.add(url);
            sortie.ressources.push({ adresse: url, depuis: adresse, type: type === "stylesheet" ? "css" : "js", corps: corps.toString("utf8") });
          }
        } catch (e) { /* une réponse illisible n'arrête pas le rendu */ }
      });

      try {
        const reponse = await page.goto(adresse, { waitUntil: "networkidle2", timeout: delai });
        if (!reponse || reponse.status() >= 400) {
          if (premiere) throw new Error(adresse + " a répondu par une erreur " + (reponse ? reponse.status() : "réseau"));
          sortie.nonAtteints.push(adresse + " : erreur " + (reponse ? reponse.status() : "réseau"));
          continue;
        }
        const finale = new URL(page.url());
        if (finale.host !== depart.host) {
          sortie.nonAtteints.push(adresse + " : redirige hors du domaine analysé");
          continue;
        }

        const html = await page.content();
        sortie.pages.push({ adresse: finale.href, html: html });

        if (premiere) {
          sortie.contrastes = await page.evaluate(mesurerContrastes);
          /* Le poids réel : tout ce que la page a chargé, images et polices comprises. */
          sortie.poidsPremierePage = poids;
        }

        const liens = await page.$$eval("a[href]", (as) => as.map((a) => a.href));
        for (const lien of liens) {
          let u;
          try { u = new URL(lien); } catch (e) { continue; }
          u.hash = "";
          if (u.host !== depart.host || !/^https?:$/.test(u.protocol) || vues.has(u.href)) continue;
          if (/\.(pdf|jpe?g|png|gif|svg|webp|ico|zip|tgz|gz|docx?|xlsx?|pptx?|odt|ods|csv|mp4|mp3|md|txt|json|xml|woff2?|ttf|css|js)$/i.test(u.pathname)) continue;
          vues.add(u.href);
          aVisiter.push(u.href);
        }
      } catch (e) {
        if (premiere) throw e;
        sortie.nonAtteints.push(adresse + " : " + e.message.split("\n")[0]);
      } finally {
        await contexte.close().catch(() => {});
      }
    }
    for (const restante of aVisiter) sortie.nonAtteints.push(restante + " : au-delà des " + pagesMax + " pages analysées");
  } finally {
    await navigateur.close().catch(() => {});
    proxy.close();
    fs.rmSync(profil, { recursive: true, force: true });
  }

  process.stdout.write(JSON.stringify(sortie));
}

principal().catch((e) => {
  process.stdout.write(JSON.stringify({ erreur: String(e && e.message || e).split("\n")[0] }));
  process.exitCode = 1;
});
