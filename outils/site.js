#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Construction du site à mettre en ligne sur chartegraphique.gov.bf
 *
 *   node outils/site.js
 *
 * Il n'y a rien à compiler : le site, ce sont les pages telles
 * qu'elles sont écrites. Cet outil ne fait que deux choses, mais
 * aucune des deux ne doit être faite à la main.
 *
 *   Il écarte ce qui n'a pas à être publié — les essais, l'outillage,
 *   les dépendances installées, le dépôt de code. Servir la racine du
 *   projet exposerait tout cela.
 *
 *   Il double les ressources sous un dossier au numéro de version.
 *   Un service peut ainsi écrire une adresse figée :
 *
 *     https://chartegraphique.gov.bf/2.0.0/css/faso.css
 *
 *   Une correction sort sous un nouveau numéro et ne réécrit jamais
 *   l'ancien : un service ne change pas d'apparence du jour au
 *   lendemain sans l'avoir décidé.
 */
"use strict";

const fs = require("fs");
const os = require("os");
const path = require("path");

const RACINE = path.join(__dirname, "..");
/* Le site ne se construit pas dans le projet : il en recopierait
   l'intégralité, et chaque page existerait deux fois sous le même nom.
   FASO_SITE permet à l'intégration continue de le placer dans son
   espace de travail. */
const SORTIE = process.env.FASO_SITE
  ? path.resolve(process.env.FASO_SITE)
  : path.join(os.tmpdir(), "fasodesign-site");
const VERSION = JSON.parse(
  fs.readFileSync(path.join(RACINE, "package.json"), "utf8")).version;

function copier(source, cible) {
  const etat = fs.statSync(source);
  if (etat.isDirectory()) {
    fs.mkdirSync(cible, { recursive: true });
    for (const e of fs.readdirSync(source)) copier(path.join(source, e), path.join(cible, e));
    return;
  }
  fs.mkdirSync(path.dirname(cible), { recursive: true });
  fs.copyFileSync(source, cible);
}

function compter(dossier) {
  let n = 0;
  let octets = 0;
  (function parcourir(d) {
    for (const e of fs.readdirSync(d, { withFileTypes: true })) {
      const p = path.join(d, e.name);
      if (e.isDirectory()) parcourir(p);
      else { n++; octets += fs.statSync(p).size; }
    }
  })(dossier);
  return { n, ko: Math.round(octets / 1024) };
}

/* Le dossier est refait à neuf : un fichier retiré du projet ne doit
   pas survivre dans le site parce qu'il traînait d'une construction
   précédente.
   On vide le dossier sans le supprimer : en production, il est créé
   par root dans un parent où le compte de construction ne peut pas
   écrire, et ce compte n'a donc pas le droit de le retirer. */
fs.mkdirSync(SORTIE, { recursive: true });
for (const e of fs.readdirSync(SORTIE)) {
  fs.rmSync(path.join(SORTIE, e), { recursive: true, force: true });
}

/* ------------------------------------------------ la documentation */

const pages = fs.readdirSync(RACINE).filter((f) => f.endsWith(".html"));
for (const p of pages) copier(path.join(RACINE, p), path.join(SORTIE, p));

for (const d of ["assets", "archives", "android"]) {
  const source = path.join(RACINE, d);
  if (fs.existsSync(source)) copier(source, path.join(SORTIE, d));
}

/* La configuration du serveur voyage avec le site : sans elle, ni
   en-têtes de sécurité, ni cache, ni autorisation pour les polices,
   ni refus des collecteurs. */
copier(path.join(RACINE, "hebergement", "apache.htaccess"),
  path.join(SORTIE, ".htaccess"));

/* Le robots.txt doit être à la racine du domaine, et nulle part
   ailleurs : demandé sous un autre chemin, il n'est jamais lu. */
copier(path.join(RACINE, "hebergement", "robots.txt"),
  path.join(SORTIE, "robots.txt"));

/* ------------------------------------------- la diffusion versionnée */

const versionne = path.join(SORTIE, VERSION);
for (const d of ["css", "js", "img", "polices"]) {
  const source = path.join(RACINE, "assets", d);
  if (fs.existsSync(source)) copier(source, path.join(versionne, d));
}

/* L'outillage de la documentation n'a rien à faire dans une adresse
   qu'un service mettra en production. */
for (const inutile of ["docs.css", "docs.js"]) {
  const p = path.join(versionne, inutile.endsWith(".css") ? "css" : "js", inutile);
  if (fs.existsSync(p)) fs.rmSync(p);
}

/* ------------------------------------------------------ le plan */

/*
 * Le robots.txt annonce un plan du site : il doit exister, faute de
 * quoi la ligne renvoie vers une page absente.
 *
 * Il ne sert pas qu'à la forme. Les moteurs restent autorisés parce
 * qu'un système de conception que personne ne trouve ne sert
 * personne ; leur donner la liste des pages est la contrepartie de
 * ce choix.
 */
const DOMAINE = "https://chartegraphique.gov.bf";
const jour = new Date().toISOString().slice(0, 10);

const plan = pages
  /* 404.html répond à une erreur : l'annoncer comme une page du site
     la ferait indexer comme telle. */
  .filter((p) => p !== "404.html")
  .map((p) => {
    const adresse = p === "index.html" ? "/" : "/" + p.replace(/\.html$/, "");
    return [
      "  <url>",
      "    <loc>" + DOMAINE + adresse + "</loc>",
      "    <lastmod>" + jour + "</lastmod>",
      "  </url>",
    ].join("\n");
  });

fs.writeFileSync(path.join(SORTIE, "sitemap.xml"), [
  '<?xml version="1.0" encoding="UTF-8"?>',
  '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
  ...plan,
  "</urlset>",
  "",
].join("\n"), "utf8");

fs.writeFileSync(path.join(SORTIE, "VERSION.txt"),
  "Charte graphique de l'administration burkinabè\n" +
  "Version " + VERSION + "\n" +
  "Construit le " + new Date().toISOString().slice(0, 10) + "\n" +
  "\n" +
  "Adresses figées, à employer par les services :\n" +
  "  /" + VERSION + "/css/tokens.css\n" +
  "  /" + VERSION + "/css/faso.css\n" +
  "  /" + VERSION + "/css/icones.css\n" +
  "  /" + VERSION + "/js/faso.js\n" +
  "  /" + VERSION + "/js/faso-amorce.js\n", "utf8");

/* ----------------------------------------------------------- bilan */

const total = compter(SORTIE);
console.log("  site construit : " + total.n + " fichiers, " + total.ko + " Ko");
console.log("    " + SORTIE);
console.log("    " + pages.length + " pages de documentation");
console.log("    diffusion figee sous /" + VERSION + "/");
console.log("    .htaccess, robots.txt et sitemap.xml a la racine");
