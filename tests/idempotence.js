// Vérifie qu'une seconde initialisation n'empile pas les gestionnaires.
// DOM minimal : on ne simule que ce que faso.js appelle réellement.

let poses = 0;   // gestionnaires posés sur des éléments
let global = 0;  // gestionnaires posés sur le document

function element(nom, attrs) {
  attrs = attrs || {};
  const el = {
    tagName: nom.toUpperCase(),
    children: [],
    childNodes: [],
    hidden: false,
    style: {},
    classList: { add() {}, remove() {}, toggle() {}, contains: () => false },
    attributes: Object.assign({}, attrs),
    addEventListener() { poses++; },
    removeEventListener() {},
    getAttribute(k) { return k in this.attributes ? this.attributes[k] : null; },
    setAttribute(k, v) { this.attributes[k] = String(v); },
    removeAttribute(k) { delete this.attributes[k]; },
    hasAttribute(k) { return k in this.attributes; },
    toggleAttribute(k, on) { on ? this.attributes[k] = "" : delete this.attributes[k]; },
    closest() { return null },
    matches() { return false },
    querySelector() { return null },
    querySelectorAll() { return [] },
    appendChild(c) { this.children.push(c); return c; },
    append() {},
    remove() {},
    focus() {},
    dispatchEvent() {},
    contains() { return false },
    getBoundingClientRect() { return { left: 0, right: 0, top: 0, bottom: 0 }; },
    get textContent() { return ""; },
    set textContent(_) {},
    get parentNode() { return null; },
    get nextElementSibling() { return null; },
    get parentElement() { return null; },
  };
  return el;
}

// Un exemplaire de chaque composant lié élément par élément
const PAR_SELECTEUR = {
  '[role="tablist"]':           [element("div")],
  "dialog.fs-modale":           [element("dialog")],
  ".fs-depot":                  [element("label")],
  ".fs-code":                   [element("div")],
  ".fs-date":                   [element("div")],
  "[data-combo]":               [element("div")],
  "table[data-triable]":        [element("table")],
  "[data-pagination]":          [element("div")],
  "[data-filtres]":             [element("div")],
  "[data-export]":              [element("div", { "data-export": "x" })],
  ".fs-notifications":          [element("div")],
  ".fs-calendrier":             [element("div")],
  "[data-compteur]":            [element("input", { "data-compteur": "z", maxlength: "10" })],
};

const documentStub = {
  readyState: "complete",
  documentElement: element("html"),
  body: element("body"),
  addEventListener() { global++; },
  removeEventListener() {},
  createElement: (n) => element(n),
  getElementById: () => null,
  contains: () => false,
  querySelector: () => null,
  querySelectorAll(sel) { return PAR_SELECTEUR[sel] || []; },
};

global0 = null;
globalThis.window = { matchMedia: () => ({ matches: false }), addEventListener() {}, Faso: null };
globalThis.document = documentStub;
globalThis.localStorage = { getItem: () => null, setItem() {} };
globalThis.MutationObserver = class { observe() {} disconnect() {} };
globalThis.requestAnimationFrame = (f) => f();

const Faso = require(require("path").join(__dirname, "..", "assets", "js", "faso.js"));

// Le chargement a déjà déclenché une initialisation.
const apresChargement = { poses, global };

Faso.initialiser();
const apres2 = { poses, global };

Faso.initialiser();
Faso.initialiser();
const apres4 = { poses, global };

console.log("  gestionnaires poses sur des elements");
console.log("    apres le chargement      :", apresChargement.poses);
console.log("    apres 2e initialisation  :", apres2.poses);
console.log("    apres 4 initialisations  :", apres4.poses);
console.log();
console.log("  gestionnaires delegues sur document");
console.log("    apres le chargement      :", apresChargement.global);
console.log("    apres 4 initialisations  :", apres4.global);
console.log();

let ko = 0;
if (apres4.poses !== apresChargement.poses) {
  console.log("  ECHEC : les liaisons par element se sont empilees"); ko++;
}
if (apres4.global !== apresChargement.global) {
  console.log("  ECHEC : la delegation globale s'est reenregistree"); ko++;
}
if (apresChargement.poses === 0) {
  console.log("  ECHEC : aucun gestionnaire pose, le test ne prouve rien"); ko++;
}
console.log(ko ? `  ${ko} echec(s)` :
  "  Idempotent : 4 initialisations posent exactement le meme nombre de gestionnaires qu'une seule.");
process.exit(ko ? 1 : 0);
