// Un onglet ajoute apres l'initialisation doit reagir comme les autres.
// DOM minimal : juste ce que initOnglets touche.

const fs = require("fs");
const CHEMIN = require("path").join(__dirname, "..", "assets", "js", "faso.js");

let ko = 0;
function verifier(libelle, obtenu, attendu) {
  const bon = obtenu === attendu;
  if (!bon) ko++;
  console.log("  " + (bon ? "ok    " : "ECHEC ") + libelle +
    (bon ? "" : "   (obtenu " + obtenu + ", attendu " + attendu + ")"));
}

/* ------------------------------------------------------------ DOM minimal */
let enAttente = [];

class Element {
  constructor(tag) {
    this.tagName = (tag || "div").toUpperCase();
    this.attrs = {};
    this.enfants = [];
    this.parentNode = null;
    this.ecouteurs = {};
    this.tabIndex = 0;
    this.hidden = false;
    this.focusRecu = 0;
    this.observateurs = [];
  }
  get textContent() {
    if (this._texte !== undefined) return this._texte;
    return this.enfants.map((e) => e.textContent).join("");
  }
  set textContent(v) { this._texte = String(v); this.enfants = []; }
  append() {
    for (const x of arguments) {
      this.appendChild(typeof x === "string"
        ? Object.assign(new Element("#text"), { _texte: x }) : x);
    }
  }
  get id() { return this.attrs.id || ""; }
  set id(v) { this.attrs.id = v; }
  setAttribute(n, v) { this.attrs[n] = String(v); }
  getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; }
  hasAttribute(n) { return n in this.attrs; }
  removeAttribute(n) { delete this.attrs[n]; }
  appendChild(e) {
    e.parentNode = this;
    this.enfants.push(e);
    this.observateurs.forEach((f) => enAttente.push(f));
    return e;
  }
  addEventListener(t, f) { (this.ecouteurs[t] = this.ecouteurs[t] || []).push(f); }
  dispatchEvent(ev) {
    ev.target = ev.target || this;
    let n = this;
    while (n) {
      (n.ecouteurs[ev.type] || []).forEach((f) => f.call(n, ev));
      n = ev.bubbles ? n.parentNode : null;
    }
    return !ev.annule;
  }
  focus() { this.focusRecu++; }
  *descendants() { for (const e of this.enfants) { yield e; yield* e.descendants(); } }
  correspond(sel) {
    let m = /^\[([\w-]+)="([^"]*)"\]$/.exec(sel);
    if (m) return this.getAttribute(m[1]) === m[2];
    m = /^\[([\w-]+)\]$/.exec(sel);
    if (m) return this.hasAttribute(m[1]);
    m = /^\.([\w-]+)$/.exec(sel);
    if (m) return (this.attrs.class || "").split(" ").includes(m[1]);
    m = /^\[id="([^"]*)"\]$/.exec(sel);
    if (m) return this.id === m[1];
    return this.tagName === sel.toUpperCase();
  }
  closest(sel) {
    let n = this;
    while (n && n.correspond) { if (n.correspond(sel)) return n; n = n.parentNode; }
    return null;
  }
  contains(n) { let x = n; while (x) { if (x === this) return true; x = x.parentNode; } return false; }
  get classList() {
    const c = (this.attrs.class || "").split(" ");
    return { contains: (x) => c.includes(x) };
  }
  querySelectorAll(sel) {
    const out = [];
    for (const d of this.descendants()) if (d.correspond(sel)) out.push(d);
    return out;
  }
  querySelector(sel) { return this.querySelectorAll(sel)[0] || null; }
  getRootNode() { let n = this; while (n.parentNode) n = n.parentNode; return n; }
}

const document = new Element("#document");
document.getElementById = (id) => document.querySelector('[id="' + id + '"]');
document.body = new Element("body");
document.contains = (n) => { let x = n; while (x) { if (x === document) return true; x = x.parentNode; } return false; };
document.createElement = (t) => new Element(t);
document.createTextNode = (t) => Object.assign(new Element("#text"), { _texte: t });
document.readyState = "complete";
document.documentElement = new Element("html");
document.activeElement = null;

class MutationObserver {
  constructor(f) { this.f = f; }
  observe(cible) { cible.observateurs.push(this.f); }
  disconnect() {}
}
function vider() { const f = enAttente; enAttente = []; f.forEach((g) => g()); }

class CustomEvent {
  constructor(type, init) {
    init = init || {};
    this.type = type; this.detail = init.detail || null;
    this.bubbles = !!init.bubbles; this.cancelable = !!init.cancelable;
    this.annule = false;
  }
  preventDefault() { if (this.cancelable) this.annule = true; }
  composedPath() { return []; }
}
const Event = CustomEvent;

global.window = { MutationObserver, CSS: null };
global.document = document;
global.MutationObserver = MutationObserver;
global.CustomEvent = CustomEvent;
global.Event = Event;
global.requestAnimationFrame = (f) => f();
global.localStorage = { getItem: () => null, setItem: () => {} };

const Faso = require(CHEMIN);

/* ------------------------------------------------------------- le montage */
function onglet(cle, actif) {
  const b = new Element("button");
  b.setAttribute("role", "tab");
  b.setAttribute("data-cle", cle);
  b.setAttribute("aria-selected", actif ? "true" : "false");
  b.setAttribute("aria-controls", "p-" + cle);
  return b;
}
function panneau(cle) {
  const p = new Element("div");
  p.id = "p-" + cle;
  return p;
}

const liste = new Element("div");
liste.setAttribute("role", "tablist");
const bloc = new Element("div");
document.appendChild(bloc);
bloc.appendChild(liste);

const a = onglet("suivi", true);
const b = onglet("pieces", false);
liste.appendChild(a); liste.appendChild(b);
bloc.appendChild(panneau("suivi"));
bloc.appendChild(panneau("pieces"));

enAttente = [];
Faso.initialiser();

verifier("panneau initial visible", document.getElementById("p-suivi").hidden, false);
verifier("panneau inactif masque", document.getElementById("p-pieces").hidden, true);

b.dispatchEvent(new CustomEvent("click", { bubbles: true }));
verifier("clic sur l'onglet 2 : il devient actif", b.getAttribute("aria-selected"), "true");

/* --- un troisieme onglet arrive apres coup, comme le ferait un @for --- */
const c = onglet("paiement", false);
bloc.appendChild(panneau("paiement"));
liste.appendChild(c);
vider();                       // le MutationObserver s'execute

verifier("onglet ajoute : hors de l'ordre de tabulation", c.tabIndex, -1);

c.dispatchEvent(new CustomEvent("click", { bubbles: true }));
verifier("onglet ajoute : le clic l'active", c.getAttribute("aria-selected"), "true");
verifier("onglet ajoute : son panneau s'affiche", document.getElementById("p-paiement").hidden, false);
verifier("l'ancien onglet se referme", document.getElementById("p-pieces").hidden, true);

/* --- le clavier connait le nouvel onglet --- */
c.dispatchEvent(Object.assign(new CustomEvent("keydown", { bubbles: true }), { key: "ArrowRight" }));
verifier("fleche droite depuis le dernier : retour au premier",
  a.getAttribute("aria-selected"), "true");

a.dispatchEvent(Object.assign(new CustomEvent("keydown", { bubbles: true }), { key: "End" }));
verifier("Fin va sur l'onglet ajoute", c.getAttribute("aria-selected"), "true");

/* --- une seconde initialisation n'empile rien --- */
const avant = c.focusRecu;
Faso.initialiser();
c.dispatchEvent(new CustomEvent("click", { bubbles: true }));
verifier("2e initialisation : pas de double activation",
  c.getAttribute("aria-selected"), "true");
verifier("2e initialisation : pas de double focus", c.focusRecu, avant);

/* --- autocompletion : un seul ecouteur, et il ferme bien --- */
const combo = new Element("div");
combo.setAttribute("data-combo", "");
combo.setAttribute("class", "fs-combo");
const champ = new Element("input");
const listeOpt = new Element("ul");
listeOpt.setAttribute("class", "fs-combo-liste");
listeOpt.id = "villes";
listeOpt.hidden = true;
for (const v of ["Ouagadougou", "Bobo-Dioulasso"]) {
  const li = new Element("li");
  li.setAttribute("class", "fs-combo-option");
  li.textContent = v;
  listeOpt.appendChild(li);
}
combo.appendChild(champ); combo.appendChild(listeOpt);
document.appendChild(combo);
enAttente = [];
Faso.initialiser();

champ.value = "oua";
champ.dispatchEvent(new CustomEvent("input", { bubbles: true }));
verifier("autocompletion : la liste s'ouvre a la frappe", listeOpt.hidden, false);

const ailleurs = new Element("div");
document.appendChild(ailleurs);
ailleurs.dispatchEvent(new CustomEvent("click", { bubbles: true }));
verifier("autocompletion : un clic exterieur la referme", listeOpt.hidden, true);

/* une option ajoutee ensuite entre dans le filtrage */
const li3 = new Element("li");
li3.setAttribute("class", "fs-combo-option");
li3.textContent = "Ouahigouya";
listeOpt.appendChild(li3);
vider();
verifier("autocompletion : option ajoutee reconnue",
  li3.getAttribute("role"), "option");

console.log();
console.log(ko ? "  " + ko + " echec(s)" : "  Les onglets ajoutes apres coup se comportent comme les autres.");
process.exit(ko ? 1 : 0);
