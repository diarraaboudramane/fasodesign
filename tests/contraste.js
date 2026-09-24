/**
 * Contraste des couples réellement employés par la feuille de styles.
 *
 * On ne teste pas des couples hypothétiques : on extrait de faso.css
 * chaque règle qui pose ensemble un fond et une couleur de texte, on
 * résout les jetons, on compose les transparences sur leur fond réel,
 * et on calcule. Les deux thèmes sont passés.
 *
 * Seuils : 4,5:1 pour le texte, 3:1 pour le contour d'un contrôle.
 */
"use strict";

const fs = require("fs");
const path = require("path");

const RACINE = path.join(__dirname, "..");
const jetonsCss = fs.readFileSync(path.join(RACINE, "assets", "css", "tokens.css"), "utf8");
const faso = fs.readFileSync(path.join(RACINE, "assets", "css", "faso.css"), "utf8");

/* ------------------------------------------------------------ jetons */

function bloc(debut) {
  const i = jetonsCss.indexOf(debut);
  if (i < 0) throw new Error("bloc introuvable : " + debut);
  const j = jetonsCss.indexOf("\n}", i);
  return jetonsCss.slice(i, j).replace(/\/\*[\s\S]*?\*\//g, "");
}

function lire(src) {
  const d = {};
  const motif = /(--[\w-]+)\s*:\s*([^;]+);/g;
  let m;
  while ((m = motif.exec(src)) !== null) d[m[1]] = m[2].trim().replace(/\s+/g, " ");
  return d;
}

const CLAIR = lire(bloc(":root {"));
const SOMBRE = Object.assign({}, CLAIR, lire(bloc('[data-theme="dark"]')));

function brut(jetons, valeur, n) {
  if (valeur == null || (n || 0) > 12) return null;
  const m = /^var\((--[\w-]+)\)$/.exec(String(valeur).trim());
  if (m) return brut(jetons, jetons[m[1]], (n || 0) + 1);
  return String(valeur).trim();
}

/** Rend un triplet RGB, transparence composée sur le fond donné. */
function rgb(jetons, valeur, fond) {
  const v = brut(jetons, valeur);
  if (!v) return null;

  let m = /^#([0-9a-fA-F]{6})$/.exec(v);
  if (m) {
    const h = m[1];
    return [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16));
  }

  m = /^rgba?\(([^)]+)\)$/.exec(v);
  if (!m) return null;

  const p = m[1].split(",").map((x) => x.trim());
  if (p.length < 3) return null;
  const a = p.length > 3 ? parseFloat(p[3]) : 1;
  const sous = fond || [255, 255, 255];
  return [0, 1, 2].map((i) => Math.round(parseFloat(p[i]) * a + sous[i] * (1 - a)));
}

function luminance(c) {
  const v = c.map((x) => {
    const n = x / 255;
    return n <= 0.04045 ? n / 12.92 : Math.pow((n + 0.055) / 1.055, 2.4);
  });
  return 0.2126 * v[0] + 0.7152 * v[1] + 0.0722 * v[2];
}

function rapport(a, b) {
  const l = [luminance(a), luminance(b)].sort((x, y) => y - x);
  return (l[0] + 0.05) / (l[1] + 0.05);
}

/* ------------------------------------------- les couples à éprouver */

const COUPLES = [];

for (const tx of ["--texte", "--texte-attenue", "--texte-faible", "--lien",
  "--lien-visite", "--action-sur-clair"]) {
  for (const fd of ["--surface", "--surface-enfoncee", "--surface-haute"]) {
    COUPLES.push([tx, fd, 4.5, "texte"]);
  }
}

for (const n of ["succes", "alerte", "danger", "info"]) {
  COUPLES.push(["--" + n + "-texte", "--" + n + "-fond", 4.5, "etat"]);
}

/* Variantes de bouton : --btn-fond et --btn-texte déclarés ensemble. */
let m;
const variantes = /(\.fs-btn--[\w-]+)\s*\{([^}]*)\}/g;
while ((m = variantes.exec(faso)) !== null) {
  const f = /--btn-fond:\s*([^;]+);/.exec(m[2]);
  const x = /--btn-texte:\s*([^;]+);/.exec(m[2]);
  if (f && x) COUPLES.push([x[1].trim(), f[1].trim(), 4.5, "bouton " + m[1]]);
}

/* Toute règle qui pose ensemble un fond et une couleur de texte. */
const regles = /([^{}]+)\{([^}]*)\}/g;
while ((m = regles.exec(faso)) !== null) {
  const corps = m[2];
  const f = /(?<![-\w])background(?:-color)?:\s*([^;]+);/.exec(corps);
  const x = /(?<![-\w])color:\s*([^;]+);/.exec(corps);
  if (!f || !x) continue;

  const vf = f[1].trim();
  const vx = x[1].trim();
  if (["transparent", "none", "inherit"].indexOf(vf) >= 0) continue;
  if (["inherit", "currentColor"].indexOf(vx) >= 0) continue;
  if (vf.indexOf("gradient") >= 0 || vf.indexOf("url(") >= 0) continue;

  const sel = m[1].trim().split("\n").pop().trim();
  COUPLES.push([vx, vf, 4.5, "regle " + sel.slice(0, 38)]);
}

for (const fd of ["--surface", "--surface-enfoncee"]) {
  COUPLES.push(["--bordure-forte", fd, 3.0, "contour"]);
}
COUPLES.push(["--focus", "--surface", 3.0, "focus"]);

/* ------------------------------------------------------------ calcul */

let echecs = 0;
let testes = 0;

for (const [nom, jetons] of [["clair", CLAIR], ["sombre", SOMBRE]]) {
  const page = rgb(jetons, jetons["--surface"]) || [255, 255, 255];

  for (const [tx, fd, seuil, genre] of COUPLES) {
    const b = rgb(jetons, jetons[fd] !== undefined ? jetons[fd] : fd, page);
    if (!b) continue;
    const a = rgb(jetons, jetons[tx] !== undefined ? jetons[tx] : tx, b);
    if (!a) continue;

    testes++;
    const r = rapport(a, b);
    if (r < seuil) {
      echecs++;
      console.log("  ECHEC " + nom + "  " + genre + "  " + tx + " sur " + fd +
        "  " + r.toFixed(2) + "  (seuil " + seuil.toFixed(1) + ")");
    }
  }
}

console.log("  " + testes + " couples eprouves, " + echecs + " sous le seuil");
process.exit(echecs ? 1 : 0);
