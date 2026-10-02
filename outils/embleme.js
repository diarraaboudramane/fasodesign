#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Déclinaisons de l'emblème
 *
 *   node outils/embleme.js
 *
 * Le fichier de référence est assets/img/armoiries.svg. Il ne suffit
 * pas à lui seul : un emblème d'État sert dans des conditions que le
 * vectoriel en couleurs ne couvre pas.
 *
 *   Une photocopieuse, un tampon, un fax, une gravure ne rendent
 *   qu'un seul canal. Il faut une version en niveaux de gris, et elle
 *   ne s'obtient pas en convertissant les couleurs par leur
 *   luminance : le rouge et le vert du drapeau donnent alors le même
 *   gris et le drapeau disparaît de l'écu. Les trois teintes
 *   nationales reçoivent donc trois tons distincts, choisis pour
 *   rester distincts.
 *
 *   Un traitement de texte, un courriel, une vignette de partage
 *   n'affichent pas de SVG de façon fiable. Il faut du matriciel, à
 *   des tailles décidées.
 *
 *   Un favicon se dessine dans 16 pixels de côté. Onze tracés y
 *   forment une tache. La charte a déjà une marque lisible à cette
 *   taille : le drapeau et son étoile. C'est elle qui sert d'icône, et
 *   l'emblème complet reste pour les tailles où il se lit.
 *
 * Les SVG sont produits sans rien installer. Les PNG demandent
 * Inkscape ; s'il est absent, le reste est produit quand même.
 */
"use strict";

const fs = require("fs");
const path = require("path");
const { execFileSync } = require("child_process");

const RACINE = path.join(__dirname, "..");
const IMG = path.join(RACINE, "assets", "img");
const SOURCE = path.join(IMG, "armoiries.svg");

/* ------------------------------------------------- niveaux de gris */

/* Les teintes nationales sont traitées à part. Converties par leur
   luminance, le rouge donnerait #707070 et le vert #717171 : un seul
   gris pour deux couleurs, et l'écu perdrait son drapeau. On les
   sépare franchement, du plus sombre au plus clair. */
const TONS_NATIONAUX = {
  "#e30613": "#4f4f4f",   // rouge
  "#00843b": "#8c8c8c",   // vert
  "#ffed00": "#e9e9e9",   // or
};

function luminance(hex) {
  const c = [1, 3, 5].map((i) => parseInt(hex.substr(i, 2), 16) / 255)
    .map((x) => (x <= 0.04045 ? x / 12.92 : Math.pow((x + 0.055) / 1.055, 2.4)));
  return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
}

function versGris(hex) {
  const bas = hex.toLowerCase();
  if (TONS_NATIONAUX[bas]) return TONS_NATIONAUX[bas];
  const v = Math.round(Math.pow(luminance(bas), 1 / 2.2) * 255);
  const h = v.toString(16).padStart(2, "0");
  return "#" + h + h + h;
}

function enGris() {
  const svg = fs.readFileSync(SOURCE, "utf8");
  const teintes = new Set();
  const gris = svg.replace(/fill="(#[0-9a-fA-F]{6})"/g, (tout, couleur) => {
    teintes.add(couleur.toLowerCase());
    return 'fill="' + versGris(couleur) + '"';
  });

  const entete = "<!-- Armoiries du Burkina Faso, niveaux de gris.\n" +
    "     Produit par outils/embleme.js depuis armoiries.svg.\n" +
    "     Pour l'impression en une seule encre, la photocopie et la\n" +
    "     telecopie. Les trois teintes du drapeau recoivent des tons\n" +
    "     separes, sans quoi l'ecu perdrait son drapeau. -->\n";

  return { contenu: gris.replace(/(<svg\b)/, entete + "$1"), teintes: teintes.size };
}

/* ------------------------------------------------------- le favicon */

/* Le drapeau dans un carré, avec l'étoile : la même composition que
   le repère national, la seule qui reste lisible à 16 pixels. Le
   tracé de l'étoile est celui du jeton --etoile-or, lu dans la
   feuille pour qu'une correction du dessin se propage ici. */
function traceEtoile() {
  const css = fs.readFileSync(path.join(RACINE, "assets", "css", "tokens.css"), "utf8");
  const jeton = /--etoile-or:\s*url\("([^"]+)"\)/.exec(css);
  if (!jeton) throw new Error("jeton --etoile-or introuvable");
  const m = /d='([^']+)'/.exec(decodeURIComponent(jeton[1]));
  if (!m) throw new Error("trace de l'etoile illisible");
  return m[1];
}

function favicon() {
  const etoile = traceEtoile();
  /* Carré de 32, étoile à 0,42 de la hauteur comme sur le repère. */
  const cote = 32;
  const taille = cote * 0.42;
  const e = taille / 100;
  const x = (cote - taille) / 2;
  const y = (cote - 95 * e) / 2;
  const n = (v) => String(Math.round(v * 1000) / 1000);

  return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">\n' +
    "  <!-- Icone de l'administration burkinabe.\n" +
    "       Le drapeau, rouge au-dessus, vert au-dessous, etoile centree.\n" +
    "       Produit par outils/embleme.js. -->\n" +
    '  <rect width="32" height="16" fill="#e30613"/>\n' +
    '  <rect y="16" width="32" height="16" fill="#00843b"/>\n' +
    '  <g transform="translate(' + n(x) + "," + n(y) + ") scale(" + n(e) + ')">\n' +
    '    <path fill="#ffed00" d="' + etoile + '"/>\n' +
    "  </g>\n" +
    "</svg>\n";
}

/* --------------------------------------------- l'écu seul, petit format */

/* Le fichier de référence est une vectorisation par nappes de tons :
   aucun objet « écu » n'y existe, et retirer des couches ne simplifie
   pas le dessin, elle l'efface. J'ai donc relevé la silhouette de
   l'écu sur le dessin lui-même — l'étendue des pixels du drapeau à
   chaque ligne, élargie de l'épaisseur du cadre, puis ramenée à la
   géométrie d'un écu : flancs droits en haut, convergents vers la
   pointe. Le contour ci-dessous est ce relevé, réduit de 202 à
   19 points sans écart visible.

   Il ne bougera que si l'emblème est redessiné. */
const ECU = {
  cadre: "190.2 225.5 88.5 107.0",
  contour: "M192.2,227.5L192.2,304.5L193.2,311.5L196.2,316.5L200.0,320.5" +
    "L206.8,322.5L212.2,325.5L218.0,327.5L222.0,330.5L244.5,330.5" +
    "L246.5,328.5L250.0,326.5L262.8,321.5L268.8,318.5L271.5,316.5" +
    "L273.0,314.5L274.8,310.5L276.8,289.5L276.8,227.5Z",
};

function ecu() {
  let svg = fs.readFileSync(SOURCE, "utf8");
  svg = svg.replace(/viewBox="[^"]*"/, 'viewBox="' + ECU.cadre + '"');

  const entete = "<!-- Ecu des armoiries du Burkina Faso, petit format.\n" +
    "     Produit par outils/embleme.js. Decoupe suivant la silhouette\n" +
    "     relevee sur le dessin de reference, pour les emplois de 16 a\n" +
    "     32 px ou l'embleme complet n'est plus lisible. -->\n";

  const decoupe = '<defs><clipPath id="ecu"><path d="' + ECU.contour +
    '"/></clipPath></defs><g clip-path="url(#ecu)">';

  const i = svg.indexOf(">", svg.indexOf("<svg")) + 1;
  return svg.slice(0, i) + "\n" + entete + decoupe +
    svg.slice(i).replace("</svg>", "</g></svg>");
}

/* ------------------------------------------------------------ PNG */

function inkscape() {
  const pistes = [
    "C:/Program Files/Inkscape/bin/inkscape.exe",
    "C:/Program Files (x86)/Inkscape/bin/inkscape.exe",
    "inkscape",
  ];
  for (const p of pistes) {
    try {
      execFileSync(p, ["--version"], { stdio: "pipe" });
      return p;
    } catch (e) { /* piste suivante */ }
  }
  return null;
}

/* Les tailles ne sont pas prises au hasard :
     512  icone d'application, vignette de partage
     256  en-tete de document, diapositive
     128  courriel, signature
      64  liste, tableau de bord
   Le favicon en 32 et le carre tactile d'iOS en 180. */
const MATRICIEL = [
  ["armoiries-512.png", SOURCE, 512],
  ["armoiries-256.png", SOURCE, 256],
  ["armoiries-128.png", SOURCE, 128],
  ["armoiries-64.png", SOURCE, 64],
];

/* --------------------------------------------------------- marche */

const grisees = enGris();
fs.writeFileSync(path.join(IMG, "armoiries-gris.svg"), grisees.contenu, "utf8");
console.log("  armoiries-gris.svg      " + grisees.teintes + " teintes converties");

const petit = ecu();
fs.writeFileSync(path.join(IMG, "armoiries-ecu.svg"), petit, "utf8");
console.log("  armoiries-ecu.svg       petit format, " +
  Math.round(Buffer.byteLength(petit) / 1024) + " Ko");

const ico = favicon();
fs.writeFileSync(path.join(IMG, "favicon.svg"), ico, "utf8");
console.log("  favicon.svg             drapeau et etoile, " +
  Math.round(Buffer.byteLength(ico)) + " octets");

const outil = inkscape();
if (!outil) {
  console.log();
  console.log("  Inkscape introuvable : les PNG ne sont pas produits.");
  console.log("  Les SVG suffisent au web ; le matriciel sert aux");
  console.log("  documents bureautiques et aux icones d'application.");
  process.exit(0);
}

/* Le format moyen : le même dessin, la géométrie allégée d'une passe.
   Une seule. À deux passes l'étoile de l'écu perd ses pointes, et on
   ne retouche pas un symbole national pour gagner 24 Ko. */
const moyen = path.join(IMG, "armoiries-moyen.svg");
fs.copyFileSync(SOURCE, moyen);
execFileSync(outil, ["--actions=select-all;path-simplify;export-filename:" +
  moyen + ";export-plain-svg;export-do", moyen], { stdio: "pipe" });
console.log("  armoiries-moyen.svg   " +
  (fs.statSync(moyen).size / 1024).toFixed(0).padStart(9) + " Ko, contre " +
  (fs.statSync(SOURCE).size / 1024).toFixed(0) + " Ko");

const aProduire = MATRICIEL.concat([
  ["armoiries-ecu-64.png", path.join(IMG, "armoiries-ecu.svg"), 64],
  ["armoiries-ecu-32.png", path.join(IMG, "armoiries-ecu.svg"), 32],
  ["favicon-32.png", path.join(IMG, "favicon.svg"), 32],
  ["favicon-180.png", path.join(IMG, "favicon.svg"), 180],
]);

console.log();
for (const [nom, source, largeur] of aProduire) {
  const cible = path.join(IMG, nom);
  execFileSync(outil, ["--export-type=png", "--export-filename=" + cible,
    "--export-width=" + largeur, source], { stdio: "pipe" });
  const ko = fs.statSync(cible).size / 1024;
  console.log("  " + nom.padEnd(22) + largeur + " px".padEnd(6) +
    ko.toFixed(1).padStart(7) + " Ko");
}
