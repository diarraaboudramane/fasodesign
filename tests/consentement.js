// Le consentement : la decision doit survivre au rechargement, le
// refus doit etre aussi facile que l'acceptation, et le service doit
// etre prevenu. On rejoue le tout sur un document simule.

const fs = require("fs");
const path = require("path");
const CHEMIN = path.join(__dirname, "..", "assets", "js", "faso.js");

let ko = 0;
function verifier(libelle, obtenu, attendu) {
  const bon = JSON.stringify(obtenu) === JSON.stringify(attendu);
  if (!bon) ko++;
  console.log("  " + (bon ? "ok    " : "ECHEC ") + libelle +
    (bon ? "" : "   (obtenu " + JSON.stringify(obtenu) +
      ", attendu " + JSON.stringify(attendu) + ")"));
}

/* ------------------------------------------------------------ document */
let enAttente = [];

class Element {
  constructor(tag) {
    this.tagName = (tag || "div").toUpperCase();
    this.attrs = {};
    this.enfants = [];
    this.parentNode = null;
    this.ecouteurs = {};
    this.hidden = false;
    this.checked = false;
    this.focusRecu = 0;
    this.observateurs = [];
    this._texte = undefined;
  }
  get id() { return this.attrs.id || ""; }
  set id(v) { this.attrs.id = v; }
  setAttribute(n, v) { this.attrs[n] = String(v); }
  getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; }
  hasAttribute(n) { return n in this.attrs; }
  removeAttribute(n) { delete this.attrs[n]; }
  get textContent() {
    if (this._texte !== undefined) return this._texte;
    return this.enfants.map((e) => e.textContent).join("");
  }
  set textContent(v) { this._texte = String(v); this.enfants = []; }
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
    if (sel.includes(",")) return sel.split(",").some((x) => this.correspond(x.trim()));
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
document.documentElement = new Element("html");
document.body = new Element("body");
document.getElementById = (id) => document.querySelector('[id="' + id + '"]');
document.contains = () => true;
document.createElement = (t) => new Element(t);
document.readyState = "complete";
document.activeElement = null;

/* Un vrai stockage de cookies : ecriture, expiration, relecture. */
let panier = {};
Object.defineProperty(document, "cookie", {
  get() {
    return Object.entries(panier).map(([k, v]) => k + "=" + v).join("; ");
  },
  set(brut) {
    const [paire, ...options] = brut.split(";").map((x) => x.trim());
    const i = paire.indexOf("=");
    const nom = paire.slice(0, i);
    const val = paire.slice(i + 1);
    const age = options.find((o) => o.toLowerCase().startsWith("max-age="));
    if (age && parseInt(age.split("=")[1], 10) <= 0) delete panier[nom];
    else panier[nom] = val;
    document.dernieresOptions = options;
  },
  configurable: true,
});

class MutationObserver { constructor(f) { this.f = f; } observe(c) { c.observateurs.push(this.f); } disconnect() {} }
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

global.window = { MutationObserver, CSS: null };
global.document = document;
global.location = { protocol: "https:" };
global.MutationObserver = MutationObserver;
global.CustomEvent = CustomEvent;
global.Event = CustomEvent;
global.requestAnimationFrame = (f) => f();
global.localStorage = { getItem: () => null, setItem: () => {} };

const Faso = require(CHEMIN);

/* ------------------------------------------------------------ le bandeau */
function bandeau() {
  const b = new Element("div");
  b.setAttribute("data-consentement", "");

  const mesure = new Element("input");
  mesure.setAttribute("data-finalite", "mesure");
  const video = new Element("input");
  video.setAttribute("data-finalite", "video");

  const detail = new Element("div");
  detail.setAttribute("data-consentement-detail", "");
  detail.hidden = true;
  detail.appendChild(mesure);
  detail.appendChild(video);

  const tout = new Element("button"); tout.setAttribute("data-consentement-tout", "");
  const rien = new Element("button"); rien.setAttribute("data-consentement-rien", "");
  const choix = new Element("button"); choix.setAttribute("data-consentement-choix", "");
  const regler = new Element("button"); regler.setAttribute("data-consentement-regler", "");

  [tout, rien, choix, regler, detail].forEach((e) => b.appendChild(e));
  document.appendChild(b);
  return { b, mesure, video, tout, rien, choix, regler, detail };
}

const clic = () => new CustomEvent("click", { bubbles: true });

/* ============================================ 1. aucune reponse encore */
panier = {};
let d = bandeau();
enAttente = [];
Faso.initialiser();

verifier("sans decision, le bandeau s'affiche", d.b.hidden, false);
verifier("lire() rend null, pas un refus", Faso.consentement.lire(), null);

/* ============================================ 2. tout refuser */
let annonces = [];
document.documentElement.addEventListener("fs:consentement",
  (ev) => annonces.push(ev.detail));

d.rien.dispatchEvent(clic());
verifier("refus : le bandeau se ferme", d.b.hidden, true);
verifier("refus : les deux finalites a false",
  Faso.consentement.lire().mesure === false &&
  Faso.consentement.lire().video === false, true);
verifier("le service est prevenu", annonces.length, 1);

/* ============================================ 3. le cookie est correct */
const options = (document.dernieresOptions || []).join(" ");
verifier("echeance de six mois posee", /Max-Age=15552000/.test(options), true);
verifier("SameSite=Lax", /SameSite=Lax/.test(options), true);
verifier("Secure en https", /Secure/.test(options), true);
verifier("Path=/", /Path=\//.test(options), true);

/* ======================== 4. la decision survit a un nouveau chargement */
const avant = Faso.consentement.lire();
d = bandeau();
enAttente = [];
Faso.initialiser();
verifier("decision retrouvee : le bandeau reste ferme", d.b.hidden, true);
verifier("la decision est la meme", Faso.consentement.lire(), avant);

/* ============================================ 5. tout accepter */
d = bandeau();
Faso.initialiser();
d.b.hidden = false;
d.tout.dispatchEvent(clic());
verifier("acceptation : mesure a true", Faso.consentement.lire().mesure, true);
verifier("acceptation : video a true", Faso.consentement.lire().video, true);

/* ============================================ 6. choix par finalite */
d = bandeau();
Faso.initialiser();
d.b.hidden = false;
d.mesure.checked = true;
d.video.checked = false;
d.choix.dispatchEvent(clic());
verifier("choix retenu : mesure acceptee", Faso.consentement.lire().mesure, true);
verifier("choix retenu : video refusee", Faso.consentement.lire().video, false);

/* ============================================ 7. le reglage se deplie */
d = bandeau();
Faso.initialiser();
verifier("le detail est replie au depart", d.detail.hidden, true);
d.regler.dispatchEvent(clic());
verifier("le reglage deplie le detail", d.detail.hidden, false);
verifier("aria-expanded suit", d.regler.getAttribute("aria-expanded"), "true");

/* ============================================ 8. revenir sur son choix */
Faso.consentement.oublier();
verifier("oublier : la question sera reposee", Faso.consentement.lire(), null);

console.log();
console.log(ko ? "  " + ko + " echec(s)"
  : "  La decision est recueillie, conservee, restituee et annoncee.");
process.exit(ko ? 1 : 0);
