// L'outil de preparation de projet : amorce/.
//
// On ne relance pas ici une installation complete, qui suppose un
// registre joignable. On verifie ce qui casse en silence : un gabarit
// que le script appelle sans qu'il existe, un chemin ecrit dans la
// page de depart qui ne sera pas publie, deux versions qui divergent.

const fs = require("fs");
const path = require("path");
const { execFileSync } = require("child_process");

const RACINE = path.join(__dirname, "..");
const AMORCE = path.join(RACINE, "amorce");

let ko = 0;
function verifier(libelle, condition, detail) {
  if (!condition) ko++;
  console.log("  " + (condition ? "ok  " : "ECHEC ") + libelle +
    (condition || !detail ? "" : "  (" + detail + ")"));
}

function lire(p) { return fs.readFileSync(p, "utf8"); }

const SCRIPT = lire(path.join(AMORCE, "index.js"));
const PAQUET = JSON.parse(lire(path.join(AMORCE, "package.json")));
const CHARTE = JSON.parse(lire(path.join(RACINE, "package.json")));

/* ------------------------------------------------------------ le script */

verifier("index.js s'analyse", (() => {
  try {
    execFileSync(process.execPath, ["--check", path.join(AMORCE, "index.js")],
      { stdio: "pipe" });
    return true;
  } catch (e) { return false; }
})());

verifier("le script est declare comme commande",
  PAQUET.bin && PAQUET.bin["create-fasodesign"] === "./index.js",
  JSON.stringify(PAQUET.bin));

verifier("le nom permet « npm create @govbf/fasodesign »",
  PAQUET.name === "@govbf/create-fasodesign", PAQUET.name);

verifier("les deux paquets portent la meme version",
  PAQUET.version === CHARTE.version,
  "amorce " + PAQUET.version + ", charte " + CHARTE.version);

verifier("le script installe bien la charte",
  SCRIPT.indexOf('"' + CHARTE.name + '"') >= 0, CHARTE.name + " introuvable");

/* ----------------------------------------------------------- les types */

const BLOC = /const TYPES = \{([\s\S]*?)\};/.exec(SCRIPT);
const TYPES = BLOC
  ? [...BLOC[1].matchAll(/(\w+):\s*"/g)].map((m) => m[1])
  : [];

verifier("les types sont lisibles dans le script", TYPES.length >= 4,
  TYPES.join(", "));

for (const type of TYPES) {
  const gabarit = type === "statique" ? "statique.html" : type + ".md";
  const descriptif = type + ".md";
  verifier("gabarit " + gabarit,
    fs.existsSync(path.join(AMORCE, "gabarits", gabarit)));
  verifier("descriptif " + descriptif,
    fs.existsSync(path.join(AMORCE, "gabarits", descriptif)));
}

verifier("gabarit charte.js",
  fs.existsSync(path.join(AMORCE, "gabarits", "charte.js")));

/* ------------------------------------------ ce que les gabarits designent */

/* Les sous-chemins importes depuis charte.js doivent figurer dans la
   carte des exports du paquet : sans quoi Node refuse l'import, et
   l'erreur ne se voit qu'a la construction du projet du developpeur. */
const CHARTE_JS = lire(path.join(AMORCE, "gabarits", "charte.js"));
for (const m of CHARTE_JS.matchAll(/import\s+(?:.+?\s+from\s+)?'(@govbf\/fasodesign[^']*)'/g)) {
  const sous = "." + m[1].slice(CHARTE.name.length);
  verifier("export " + m[1],
    Object.prototype.hasOwnProperty.call(CHARTE.exports, sous),
    sous + " absent de la carte des exports");
}

/* Les fichiers que la page de depart appelle doivent etre publies :
   un chemin correct dans le depot mais absent de l'archive donnerait
   une page sans styles, sans erreur visible cote serveur. */
const declares = new Set(CHARTE.files);
function publie(chemin) {
  if (declares.has(chemin)) return true;
  for (const d of declares) if (chemin === d || chemin.startsWith(d + "/")) return true;
  return false;
}

const PAGE = lire(path.join(AMORCE, "gabarits", "statique.html"));
const prefixe = "node_modules/" + CHARTE.name + "/";
let chemins = 0;
for (const m of PAGE.matchAll(/(?:src|href)="(node_modules\/[^"]+)"/g)) {
  chemins++;
  const relatif = m[1].slice(prefixe.length);
  verifier("publie : " + relatif,
    m[1].startsWith(prefixe) && fs.existsSync(path.join(RACINE, relatif)) &&
    publie(relatif));
}
verifier("la page de depart designe bien la charte", chemins >= 5,
  chemins + " chemin(s)");

/* ---------------------------------------------------- l'archive de l'outil */

const emportes = new Set(PAQUET.files);
verifier("les gabarits partent avec l'outil", emportes.has("gabarits"));
verifier("les conditions partent avec l'outil", emportes.has("LISEZMOI.txt"));
verifier("LISEZMOI.txt existe", fs.existsSync(path.join(AMORCE, "LISEZMOI.txt")));

console.log();
console.log(ko ? "  " + ko + " echec(s)" : "  L'outil de preparation est verifie.");
process.exit(ko ? 1 : 0);
