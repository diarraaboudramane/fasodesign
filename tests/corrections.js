// Preuves des corrections : resolution, racine fantome, verrou de
// defilement compte, conservation de la page a un re-rendu.
// Le code teste est celui du fichier, decoupe et evalue tel quel.

const fs = require("fs");
const SRC = fs.readFileSync(
  require("path").join(__dirname, "..", "assets", "js", "faso.js"),
  "utf8");

let ko = 0;
function verifier(libelle, obtenu, attendu) {
  const bon = obtenu === attendu;
  if (!bon) ko++;
  console.log("  " + (bon ? "ok  " : "ECHEC ") + libelle +
    (bon ? "" : "  (obtenu " + obtenu + ", attendu " + attendu + ")"));
}

/* ---------------------------------------------------------- arbre minimal */
class N {
  constructor(id) {
    this.id = id || null;
    this.enfants = [];
    this.parentNode = null;
    this.host = null;
  }
  ajouter() {
    for (const e of arguments) { e.parentNode = this; this.enfants.push(e); }
    return this;
  }
  *descendants() {
    for (const e of this.enfants) { yield e; yield* e.descendants(); }
  }
  querySelectorAll(sel) {
    const m = /^\[id="(.*)"\]$/.exec(sel);
    const out = [];
    for (const d of this.descendants()) if (d.id === m[1]) out.push(d);
    return out;
  }
  querySelector(sel) { return this.querySelectorAll(sel)[0] || null; }
  getRootNode() { let n = this; while (n.parentNode) n = n.parentNode; return n; }
}

const document = new N("#document");
document.getElementById = function (id) {
  return document.querySelector('[id="' + id + '"]');
};
document.body = { style: { overflow: "" } };

const window = { MutationObserver: null };
const NAVIGATEUR = true;

/* -------------------------------- extraction du bloc reel des utilitaires */
const debut = SRC.indexOf("  function echapper(valeur) {");
const fin = SRC.indexOf("\n  }", SRC.indexOf("function libererDefilement")) + 4;
if (debut < 0 || fin < 4) { console.log("  ECHEC : bloc introuvable"); process.exit(1); }
eval(SRC.slice(debut, fin).replace(/^ {2}/gm, ""));

/* ================================================== 1. identifiant unique */
// main > section > bouton ; la cible est ailleurs dans le document
const cible = new N("panneau");
const bouton = new N("btn");
const section = new N(null).ajouter(bouton);
document.ajouter(section, new N(null).ajouter(cible));

verifier("identifiant unique : meme resultat que getElementById",
  resoudre(bouton, "panneau"), document.getElementById("panneau"));
verifier("identifiant absent : rien",
  resoudre(bouton, "inconnu"), null);

/* ============================================== 2. identifiants dupliques */
// deux lignes d'une liste, chacune avec sa modale « confirmer »
const modaleA = new N("confirmer");
const btnA = new N(null);
const ligneA = new N(null).ajouter(btnA, modaleA);

const modaleB = new N("confirmer");
const btnB = new N(null);
const ligneB = new N(null).ajouter(btnB, modaleB);

const liste = new N(null).ajouter(ligneA, ligneB);
document.ajouter(liste);

verifier("doublon : la ligne 1 ouvre sa propre modale",
  resoudre(btnA, "confirmer"), modaleA);
verifier("doublon : la ligne 2 ouvre la sienne",
  resoudre(btnB, "confirmer"), modaleB);

/* =================================================== 3. racine fantome */
const interne = new N("aide");
const btnFantome = new N(null);
const racineFantome = new N(null).ajouter(btnFantome, interne);
const hote = new N(null);
racineFantome.host = hote;
racineFantome.parentNode = null;      // comme un vrai ShadowRoot
document.ajouter(hote);

verifier("racine fantome : la cible interne est trouvee",
  resoudre(btnFantome, "aide"), interne);

const dehors = new N("lointain");
document.ajouter(dehors);
verifier("racine fantome : repli sur le document",
  resoudre(btnFantome, "lointain"), dehors);

/* ========================================== 4. verrou de defilement compte */
document.body.style.overflow = "auto";        // valeur posee par le projet
verrouillerDefilement();
verifier("un panneau : defilement bloque", document.body.style.overflow, "hidden");
verrouillerDefilement();
libererDefilement();
verifier("deux panneaux, un ferme : toujours bloque",
  document.body.style.overflow, "hidden");
libererDefilement();
verifier("le dernier ferme : valeur d'origine rendue",
  document.body.style.overflow, "auto");
libererDefilement();
verifier("liberation en trop : sans effet", document.body.style.overflow, "auto");

/* ================================================ 5. annonce annulable */
let vu = null;
class EvenementFactice {
  constructor(type, init) { this.type = type; this.detail = init.detail; this.annule = false; }
  preventDefault() { this.annule = true; }
}
global.CustomEvent = EvenementFactice;

const piloteA = new N(null);
piloteA.dispatchEvent = function (ev) { vu = ev; return !ev.annule; };
verifier("sans interception : la charte ecrit", repris(piloteA, "onglet", { i: 2 }), false);
verifier("le detail est transmis", vu.detail.i, 2);

const noms = [];
const piloteC = new N(null);
piloteC.dispatchEvent = function (ev) { noms.push(ev.type); return true; };
repris(piloteC, "onglet", null);
verifier("annonce emise sous les deux noms", noms.join(" "), "fs:onglet fs-onglet");

const piloteD = new N(null);
piloteD.dispatchEvent = function (ev) {
  if (ev.type === "fs-onglet") ev.preventDefault();   // liaison Angular
  return !ev.annule;
};
verifier("annuler la forme a trait d'union suffit",
  repris(piloteD, "onglet", null), true);

const piloteB = new N(null);
piloteB.dispatchEvent = function (ev) { ev.preventDefault(); return false; };
verifier("interception : la charte s'abstient", repris(piloteB, "jauge", null), true);

/* ============================ 6. conservation de la page a un re-rendu */
const d2 = SRC.indexOf("      function apres(rejeu, annoncer) {");
const f2 = SRC.indexOf("\n      }", d2) + 8;
if (d2 < 0) { console.log("  ECHEC : apres() introuvable"); process.exit(1); }

let demandee = null;
let compte = 40;
let dernierCompte = 40;
let page = 7;
function afficher(n) { demandee = n; }
function lignes() { return { length: compte }; }
eval(SRC.slice(d2, f2).replace(/^ {6}/gm, ""));

apres(true, false);
verifier("re-rendu, meme nombre de lignes : on reste page 7", demandee, 7);

compte = 25;
apres(true, false);
verifier("re-rendu, jeu different : retour page 1", demandee, 1);

compte = 40;
apres(false, false);
verifier("tri ou filtre : retour page 1", demandee, 1);

console.log();
console.log(ko ? "  " + ko + " echec(s)" : "  Toutes les corrections sont verifiees.");
process.exit(ko ? 1 : 0);
