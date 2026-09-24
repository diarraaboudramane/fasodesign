// React ne relit pas le champ : il compare a ce que son propre
// accesseur a memorise. Une valeur ecrite par cet accesseur passe
// donc inapercue. Ce test rejoue le mecanisme exact de React.

const fs = require("fs");
const CHEMIN = require("path").join(__dirname, "..", "assets", "js", "faso.js");
const SRC = fs.readFileSync(CHEMIN, "utf8");

let ko = 0;
function verifier(libelle, obtenu, attendu) {
  const bon = obtenu === attendu;
  if (!bon) ko++;
  console.log("  " + (bon ? "ok    " : "ECHEC ") + libelle +
    (bon ? "" : "   (obtenu " + obtenu + ", attendu " + attendu + ")"));
}

/* -------------------------- un champ, avec l'accesseur natif du prototype */
class HTMLInputElement {
  constructor() { this._valeur = ""; this.ecouteurs = {}; }
  addEventListener(t, f) { (this.ecouteurs[t] = this.ecouteurs[t] || []).push(f); }
  dispatchEvent(ev) { (this.ecouteurs[ev.type] || []).forEach((f) => f(ev)); return true; }
}
Object.defineProperty(HTMLInputElement.prototype, "value", {
  get() { return this._valeur; },
  set(v) { this._valeur = String(v); },
  configurable: true,
});

global.HTMLInputElement = HTMLInputElement;
global.window = {};
global.document = {};
global.Event = class { constructor(type) { this.type = type; } };

/* ------------------------------- le suivi de valeur, tel que React l'installe */
function poserSuiviReact(noeud) {
  const natif = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, "value");
  const suivi = { memorise: noeud.value, changements: 0 };

  // React redefinit la propriete SUR LE NOEUD, masquant celle du prototype.
  Object.defineProperty(noeud, "value", {
    get() { return natif.get.call(this); },
    set(v) { suivi.memorise = String(v); natif.set.call(this, v); },
    configurable: true,
  });

  // onChange n'est appele que si la valeur lue differe de la memorisee.
  noeud.addEventListener("input", function () {
    if (natif.get.call(noeud) === suivi.memorise) return;
    suivi.memorise = natif.get.call(noeud);
    suivi.changements++;
  });

  return suivi;
}

/* ---------------------------------- le code reel, decoupe dans le fichier */
const debut = SRC.indexOf("  var ECRITURE_SCRIPT = false;");
const fin = SRC.indexOf("\n  }", SRC.indexOf("function poserValeur")) + 4;
if (debut < 0 || fin < 4) { console.log("  ECHEC : bloc introuvable"); process.exit(1); }
const NAVIGATEUR = true;
eval(SRC.slice(debut, fin).replace(/^ {2}/gm, ""));

/* =========================== 1. l'ecriture naive, celle d'avant la correction */
const naif = new HTMLInputElement();
const suiviNaif = poserSuiviReact(naif);
naif.value = "Ouagadougou";                 // affectation ordinaire
signalerSaisie(naif);
verifier("ecriture ordinaire : React ne voit rien", suiviNaif.changements, 0);
verifier("  (le champ affiche pourtant la valeur)", naif.value, "Ouagadougou");

/* ================================= 2. l'ecriture de la charte, apres correction */
const propre = new HTMLInputElement();
const suiviPropre = poserSuiviReact(propre);
poserValeur(propre, "Bobo-Dioulasso");
verifier("poserValeur : React est averti", suiviPropre.changements, 1);
verifier("  et le champ porte bien la valeur", propre.value, "Bobo-Dioulasso");

/* ================================ 3. rien ne casse sans suivi de valeur */
const nu = new HTMLInputElement();
let recus = [];
nu.addEventListener("input", () => recus.push("input"));
nu.addEventListener("change", () => recus.push("change"));
poserValeur(nu, "Koudougou");
verifier("sans cadriciel : la valeur est posee", nu.value, "Koudougou");
verifier("sans cadriciel : les deux evenements partent", recus.join(","), "input,change");

/* ============================ 4. nos propres ecouteurs restent endormis */
let filtrages = 0;
const combo = new HTMLInputElement();
poserSuiviReact(combo);
combo.addEventListener("input", function () {
  if (ECRITURE_SCRIPT) return;              // la garde reelle de la charte
  filtrages++;
});
poserValeur(combo, "Ouahigouya");
verifier("la liste ne se rouvre pas", filtrages, 0);
verifier("le drapeau retombe", ECRITURE_SCRIPT, false);

console.log();
console.log(ko ? "  " + ko + " echec(s)"
  : "  React est averti, nos ecouteurs ne le sont pas.");
process.exit(ko ? 1 : 0);
