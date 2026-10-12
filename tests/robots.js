// Le robots.txt, relu comme un analyseur le lit.
//
// Un robots.txt mal groupe ne se voit pas : le fichier est lisible,
// les noms sont tous la, et pourtant le refus ne s'applique a
// personne. Une ligne vide de trop separe un « User-agent » de son
// « Disallow », et le groupe devient vide.
//
// On applique donc la RFC 9309, telle que les analyseurs reels la
// mettent en oeuvre : un groupe est une suite de lignes « User-agent »
// suivie de ses regles, et c'est un « User-agent » venant apres une
// regle qui ouvre le groupe suivant. Ni les lignes vides ni les
// commentaires ne decoupent quoi que ce soit — s'y fier donnerait un
// decoupage qui n'est celui d'aucun collecteur.

const fs = require("fs");
const path = require("path");

const RACINE = path.join(__dirname, "..");
const TEXTE = fs.readFileSync(path.join(RACINE, "hebergement", "robots.txt"), "utf8");

let ko = 0;
function verifier(libelle, condition, detail) {
  if (!condition) ko++;
  console.log("  " + (condition ? "ok  " : "ECHEC ") + libelle +
    (condition || !detail ? "" : "  (" + detail + ")"));
}

/* ------------------------------------------------------- analyse */

function groupes(texte) {
  const out = [];
  let courant = null;
  let attendRegles = false;

  for (const brute of texte.split(/\r?\n/)) {
    const ligne = brute.replace(/#.*$/, "").trim();
    if (!ligne) continue;

    const sep = ligne.indexOf(":");
    if (sep < 0) continue;
    const champ = ligne.slice(0, sep).trim().toLowerCase();
    const valeur = ligne.slice(sep + 1).trim();

    if (champ === "user-agent") {
      /* Un « User-agent » qui suit des regles ouvre un nouveau
         groupe ; celui qui suit un autre « User-agent » s'y ajoute. */
      if (!courant || attendRegles) {
        courant = { agents: [], regles: [] };
        out.push(courant);
        attendRegles = false;
      }
      courant.agents.push(valeur.toLowerCase());
      continue;
    }

    if (champ === "disallow" || champ === "allow") {
      if (!courant) continue;      // regle hors de tout groupe : ignoree
      attendRegles = true;
      courant.regles.push({ champ: champ, chemin: valeur });
    }
  }
  return out;
}

const GROUPES = groupes(TEXTE);
verifier("le fichier se decoupe en groupes", GROUPES.length >= 5,
  GROUPES.length + " groupe(s)");

/* Aucun groupe ne doit etre vide de regles : ce serait une liste de
   noms qui ne refuse rien. */
const creux = GROUPES.filter((g) => !g.regles.length);
verifier("aucun groupe sans regle", creux.length === 0,
  creux.map((g) => g.agents.join(", ")).join(" | "));

/* ------------------------------------- ce qui doit etre refuse */

function refuseTout(nom) {
  const g = GROUPES.find((x) => x.agents.includes(nom.toLowerCase()));
  if (!g) return false;
  return g.regles.some((r) => r.champ === "disallow" && r.chemin === "/");
}

const REFUSES = [
  "GPTBot", "ClaudeBot", "anthropic-ai", "Claude-Web", "CCBot",
  "Google-Extended", "Applebot-Extended", "Meta-ExternalAgent",
  "Bytespider", "Amazonbot", "PerplexityBot", "CCBot", "Scrapy",
  "Perplexity-User", "MistralAI-User",
  "HTTrack", "Offline Explorer", "sqlmap", "Nikto",
];
let manques = [];
for (const nom of REFUSES) if (!refuseTout(nom)) manques.push(nom);
verifier(REFUSES.length + " agents refuses par le fichier", manques.length === 0,
  manques.join(", "));

/* --------------------------- l'exception faite aux assistants */

/* /charte.md est ecrit pour les assistants : ils doivent pouvoir le
   lire, et eux seuls. Un collecteur d'entrainement qui heriterait de
   l'exception emporterait toute la charte d'une requete. */
function ouvre(nom, chemin) {
  const g = GROUPES.find((x) => x.agents.includes(nom.toLowerCase()));
  return !!g && g.regles.some((r) => r.champ === "allow" && r.chemin === chemin);
}

const ASSISTANTS = ["Perplexity-User", "MistralAI-User", "OAI-SearchBot", "Claude-SearchBot"];
const fermes = ASSISTANTS.filter((n) => !ouvre(n, "/charte.md"));
verifier("les assistants peuvent lire /charte.md", fermes.length === 0, fermes.join(", "));

const COLLECTEURS = ["GPTBot", "ClaudeBot", "CCBot", "Google-Extended", "Bytespider", "HTTrack", "Scrapy"];
const ouverts = COLLECTEURS.filter((n) => GROUPES
  .filter((x) => x.agents.includes(n.toLowerCase()))
  .some((g) => g.regles.some((r) => r.champ === "allow")));
verifier("aucun collecteur n'herite de l'exception", ouverts.length === 0, ouverts.join(", "));

/* Le refus serveur doit porter la meme exception, et pour les memes
   agents : sinon le robots.txt promet ce que le serveur refuse. */
const HTACCESS = fs.readFileSync(path.join(RACINE, "public", ".htaccess"), "utf8");
const NGINX = fs.readFileSync(path.join(RACINE, "hebergement", "faso-robots.conf"), "utf8");
verifier("Apache ouvre /charte.md aux seuls assistants",
  HTACCESS.indexOf("Require env faso_assistant") >= 0 &&
  HTACCESS.indexOf("THE_REQUEST") >= 0 && HTACCESS.indexOf("/charte\\.md") >= 0 &&
  ASSISTANTS.every((n) => HTACCESS.indexOf('"' + n + '" faso_collecteur faso_assistant') >= 0) &&
  COLLECTEURS.every((n) => HTACCESS.indexOf('"' + n + '" faso_collecteur faso_assistant') < 0));
verifier("nginx ouvre /charte.md aux seuls assistants",
  /map "\$faso_assistant\$request_uri" \$faso_ouvert/.test(NGINX) &&
  ASSISTANTS.every((n) => NGINX.indexOf(n) >= 0) &&
  !/\$faso_assistant \{[^}]*GPTBot/.test(NGINX));

/* ------------------------- les assistants autorises partout */

/* ChatGPT et Claude, a la demande d'une personne, lisent tout le site.
   Ils ont leur groupe dans le robots.txt, sans refus de la racine, et
   aucun motif du serveur ne doit les reconnaitre : les motifs sont des
   sous-chaines sans casse, et « Claude » ou « ChatGPT » suffirait a
   les refuser de nouveau sans que rien ne le signale. Les agents
   ci-dessous sont ceux qu'envoient reellement OpenAI et Anthropic. */
const AUTORISES = {
  "ChatGPT-User": "Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ChatGPT-User/1.0; +https://openai.com/bot",
  "Claude-User": "Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Claude-User/1.0; +Claude-User@anthropic.com)",
};
const MOTIFS = [...HTACCESS.matchAll(/SetEnvIfNoCase User-Agent "([^"]+)"/g)].map((m) => new RegExp(m[1], "i"));
for (const [nom, agent] of Object.entries(AUTORISES)) {
  const g = GROUPES.find((x) => x.agents.includes(nom.toLowerCase()));
  verifier(nom + " a son groupe, sans refus de la racine",
    !!g && !g.agents.includes("*") && !refuseTout(nom));
  const pris = MOTIFS.filter((m) => m.test(agent)).map(String);
  verifier(nom + " n'est reconnu par aucun motif de refus", pris.length === 0, pris.join(", "));
}

/* ------------------------------------ ce qui doit rester permis */

/*
 * Un moteur qui n'a pas son propre groupe tombe dans « * ». Il ne
 * doit y trouver ni « Disallow: / », ni refus de la racine.
 *
 * Applebot est le cas qui compte : Applebot-Extended est refuse, et
 * une comparaison par sous-chaine plutot que par nom exact les
 * confondrait. Un moteur retire de l'index ne se remarque que des
 * semaines plus tard, quand plus personne ne trouve la charte.
 */
const ETOILE = GROUPES.find((g) => g.agents.includes("*"));
verifier("un groupe « * » existe", !!ETOILE);

if (ETOILE) {
  verifier("« * » ne refuse pas la racine",
    !ETOILE.regles.some((r) => r.champ === "disallow" && r.chemin === "/"),
    JSON.stringify(ETOILE.regles));

  const refuses = ETOILE.regles
    .filter((r) => r.champ === "disallow" && r.chemin)
    .map((r) => r.chemin);
  verifier("les pages de documentation restent parcourables",
    !refuses.some((c) => ["/", "/index.html", "*.html"].includes(c)),
    refuses.join(", "));
}

for (const moteur of ["Googlebot", "Bingbot", "DuckDuckBot", "Applebot", "Qwantbot"]) {
  verifier(moteur + " n'a pas de groupe de refus", !refuseTout(moteur));
}

/* ------------------------------------------------ le plan du site */

const plan = /^Sitemap:\s*(\S+)$/mi.exec(TEXTE);
verifier("le plan du site est annonce", !!plan);
if (plan) {
  /* Annoncer un plan absent enverrait chaque moteur sur une page
     d'erreur. site.js l'engendre depuis la liste reelle des pages. */
  const source = fs.readFileSync(path.join(RACINE, "outils", "site.js"), "utf8");
  verifier("le plan annonce est bien engendre",
    source.indexOf('"sitemap.xml"') >= 0,
    "site.js n'ecrit pas sitemap.xml");
}

/* ------------------------------------------- la mise en production */

/* Les fragments nginx nomment $faso_refuse et les zones de debit,
   definis dans faso-robots.conf : sans lui dans le contexte « http »,
   nginx ne demarre pas, ou la protection n'existe qu'au depot de code.
   Chaque fragment doit donc le dire a qui l'installe. */
for (const fragment of ["nginx.conf", "nginx-laravel.conf"]) {
  const texte = fs.readFileSync(path.join(RACINE, "hebergement", fragment), "utf8");
  verifier(fragment + " annonce qu'il requiert faso-robots.conf",
    texte.indexOf("$faso_refuse") >= 0 && texte.indexOf("faso-robots.conf") >= 0);
}

console.log();
console.log(ko ? "  " + ko + " echec(s)" : "  Le robots.txt est bien forme.");
process.exit(ko ? 1 : 0);
