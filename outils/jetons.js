#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Projection de tokens.css vers assets/jetons.json
 *
 *   node outils/jetons.js              réécrit assets/jetons.json
 *   node outils/jetons.js --verifier   n'écrit rien, sort en 1 si le
 *                                      fichier ne correspond plus
 *
 * Le fichier JSON existait déjà, entretenu à la main : la valeur de
 * police-mono y avait divergé de celle du CSS sans que rien ne le
 * signale. Cet outil supprime la possibilité même de la divergence,
 * et la vérification a sa place dans la recette avant publication.
 *
 * Node seul, aucune bibliothèque à installer.
 */
"use strict";

const fs = require("fs");
const path = require("path");

const RACINE = path.join(__dirname, "..");
const SOURCE = path.join(RACINE, "assets", "css", "tokens.css");
const SORTIE = path.join(RACINE, "assets", "jetons.json");

/* Les familles se déduisent du nom, et non de la place dans le
   fichier : un jeton déplacé lors d'une réorganisation ne doit pas
   changer de famille. */
const FAMILLES = [
  ["palette", /^--(rouge|vert|or|ocre|bleu|neutre)-|^--etoile-/],
  ["typographie", /^--(police|taille|graisse|interligne|approche)-/],
  ["espacement", /^--(espace-|section-y|gouttiere)/],
  ["mise-en-page", /^--(largeur-|colonnes$|cible-min$|mesure$)/],
  ["forme", /^--(rayon|ombre|trait)-/],
  ["mouvement", /^--(duree|courbe)-/],
  ["superposition", /^--plan-/],
  ["serie", /^--serie-/],
];

const DESCRIPTIONS = {
  palette: "Valeurs chromatiques absolues. Jamais consommees directement par un composant.",
  role: "Ce que les composants consomment. Changer de theme revient a reaffecter ces jetons.",
  typographie: "Familles, echelle, graisses, interlignes et approches.",
  espacement: "Echelle de base 4 px et respirations.",
  "mise-en-page": "Largeurs de conteneur, gouttiere, grille, cible tactile.",
  forme: "Rayons, ombres et epaisseurs de trait.",
  mouvement: "Durees et courbes d'acceleration.",
  superposition: "Plans d'empilement.",
  serie: "Couleurs de series pour la visualisation de donnees.",
};

const ORDRE = ["palette", "typographie", "espacement", "forme", "mouvement",
  "superposition", "mise-en-page", "role", "serie"];

function famille(nom) {
  for (const [cle, motif] of FAMILLES) {
    if (motif.test(nom)) return cle;
  }
  return "role";
}

/** Contenu du premier bloc :root, commentaires retirés. */
function blocRacine(css) {
  const debut = css.indexOf(":root {");
  if (debut < 0) throw new Error("bloc :root introuvable dans tokens.css");
  const fin = css.indexOf("\n}", debut);
  return css.slice(debut, fin).replace(/\/\*[\s\S]*?\*\//g, "");
}

function construire() {
  const css = fs.readFileSync(SOURCE, "utf8");
  const racine = blocRacine(css);

  const jetons = {};
  for (const cle of ORDRE) jetons[cle] = {};

  let total = 0;
  const motif = /(--[\w-]+)\s*:\s*([^;]+);/g;
  let m;
  while ((m = motif.exec(racine)) !== null) {
    const nom = m[1];
    const valeur = m[2].trim().replace(/\s+/g, " ");
    jetons[famille(nom)][nom.slice(2)] = { valeur };
    total++;
  }

  const paquet = JSON.parse(fs.readFileSync(path.join(RACINE, "package.json"), "utf8"));

  return {
    nom: "Charte graphique de l'administration burkinabè",
    version: paquet.version,
    date: new Date().toISOString().slice(0, 10),
    source: "assets/css/tokens.css",
    avertissement:
      "Projection de la feuille de jetons CSS, qui reste la source faisant foi. " +
      "Fichier produit par outils/jetons.js ; ne jamais le modifier directement. " +
      "La verification « node outils/jetons.js --verifier » fait partie de la recette.",
    total,
    familles: DESCRIPTIONS,
    ressources: {
      css: "assets/css/tokens.css",
      composants: "assets/css/faso.css",
      icones: "assets/css/icones.css",
    },
    jetons,
  };
}

const attendu = JSON.stringify(construire(), null, 2) + "\n";

if (process.argv.includes("--verifier")) {
  const actuel = fs.existsSync(SORTIE) ? fs.readFileSync(SORTIE, "utf8") : "";

  /* La date de production change chaque jour : elle est neutralisee
     avant comparaison, sans quoi la verification echouerait le
     lendemain d'une generation sans qu'aucun jeton n'ait bouge. */
  const sansDate = (t) => t.replace(/"date": "[^"]*"/, '"date": ""');

  if (sansDate(actuel) === sansDate(attendu)) {
    console.log("  jetons.json est conforme a tokens.css");
    process.exit(0);
  }
  console.error("  jetons.json ne correspond plus a tokens.css.");
  console.error("  Relancer : node outils/jetons.js");
  process.exit(1);
}

fs.writeFileSync(SORTIE, attendu, "utf8");
const d = JSON.parse(attendu);
console.log("  " + d.total + " jetons ecrits dans assets/jetons.json");
for (const [f, v] of Object.entries(d.jetons)) {
  console.log("    " + f.padEnd(14) + Object.keys(v).length);
}
