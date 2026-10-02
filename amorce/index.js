#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Préparation d'un projet
 *
 *   npm create @govbf/fasodesign@latest
 *   npm create @govbf/fasodesign@latest mon-service -- --type=statique --oui
 *
 * Trois choses, et rien d'autre : faire accepter les conditions
 * d'emploi, écrire les fichiers de départ, installer la charte.
 *
 * Le premier point n'est pas une formalité. Le paquet porte les
 * armoiries du Burkina Faso, qui sont un symbole de l'État. Personne
 * ne doit pouvoir les installer sans avoir lu à quelles conditions il
 * en dispose. L'acceptation se donne à la main, ou par la variable
 * FASODESIGN_ACCEPTE_LICENCE pour une chaîne d'intégration.
 *
 * Aucune dépendance : Node seul, et les questions passent par
 * readline.
 */
"use strict";

const fs = require("fs");
const path = require("path");
const readline = require("readline");
const { execFileSync } = require("child_process");

const PAQUET = "@govbf/fasodesign";
const GABARITS = path.join(__dirname, "gabarits");

const TYPES = {
  statique: "Page servie telle quelle, sans outillage",
  react: "React, avec Vite",
  angular: "Angular",
  laravel: "Laravel, avec Blade et Vite",
};

/* Où le fichier d'amorçage va se poser, selon ce que le cadriciel
   attend. Laravel range ses sources ailleurs que les deux autres. */
const SOURCES = {
  react: "src/charte.js",
  angular: "src/charte.js",
  laravel: "resources/js/charte.js",
};

/* ------------------------------------------------------------ sortie */

const gras = (t) => "\u001b[1m" + t + "\u001b[0m";
const faible = (t) => "\u001b[2m" + t + "\u001b[0m";

function dire(t) { process.stdout.write(t + "\n"); }
function erreur(t) { process.stderr.write("\n  " + t + "\n\n"); }

/* ------------------------------------------------------- arguments */

function arguments_() {
  const bruts = process.argv.slice(2);
  const opt = { dossier: null, type: null, oui: false, depot: null };

  for (const a of bruts) {
    if (a === "--oui" || a === "-y") opt.oui = true;
    else if (a.startsWith("--type=")) opt.type = a.slice(7);
    else if (a.startsWith("--depot=")) opt.depot = a.slice(8);
    else if (a === "--aide" || a === "-h") opt.aide = true;
    else if (!a.startsWith("-") && !opt.dossier) opt.dossier = a;
  }
  return opt;
}

function aide() {
  dire("");
  dire("  " + gras("npm create @govbf/fasodesign@latest") + " [dossier] [options]");
  dire("");
  dire("  --type=<" + Object.keys(TYPES).join("|") + ">");
  dire("  --depot=<adresse>   dépôt npm interne, si le projet en emploie un");
  dire("  --oui               accepte les conditions sans les redemander");
  dire("");
  dire("  " + faible("En intégration continue : FASODESIGN_ACCEPTE_LICENCE=oui"));
  dire("");
}

/* -------------------------------------------------------- questions */

function questionneur() {
  return readline.createInterface({ input: process.stdin, output: process.stdout });
}

function demander(rl, question, defaut) {
  return new Promise((resoudre) => {
    const suffixe = defaut ? faible(" (" + defaut + ")") : "";
    rl.question("  " + question + suffixe + " ", (reponse) => {
      resoudre(reponse.trim() || defaut || "");
    });
  });
}

/* ------------------------------------------------------- conditions */

const CONDITIONS = [
  "",
  "  " + gras("Conditions d'emploi"),
  "",
  "  Cette charte porte les armoiries du Burkina Faso, le drapeau national",
  "  et la devise de l'État. Ce sont des symboles de souveraineté.",
  "",
  "  En l'installant, vous vous engagez à ne l'employer que pour un service",
  "  de l'administration burkinabè ou pour le compte de celle-ci, à ne pas",
  "  modifier les emblèmes, leurs couleurs ni leurs proportions, et à ne pas",
  "  laisser croire à un caractère officiel là où il n'y en a pas.",
  "",
  "  Le détail figure dans LISEZMOI-PAQUET.txt et dans la page Gouvernance",
  "  de la documentation.",
  "",
];

async function accepter(rl, opt) {
  if (opt.oui || process.env.FASODESIGN_ACCEPTE_LICENCE === "oui") return true;

  CONDITIONS.forEach(dire);
  const r = await demander(rl, "Acceptez-vous ces conditions ? [oui/non]", "non");
  return /^(o|oui|y|yes)$/i.test(r);
}

/* --------------------------------------------------------- gabarits */

function ecrire(cible, chemin, contenu) {
  const complet = path.join(cible, chemin);
  fs.mkdirSync(path.dirname(complet), { recursive: true });
  fs.writeFileSync(complet, contenu, "utf8");
  dire("    " + chemin);
}

function gabarit(nom, remplacements) {
  let t = fs.readFileSync(path.join(GABARITS, nom), "utf8");
  for (const [cle, valeur] of Object.entries(remplacements || {})) {
    t = t.split("{{" + cle + "}}").join(valeur);
  }
  return t;
}

function preparer(cible, type, nom, depot) {
  dire("");
  dire("  " + gras("Fichiers écrits"));

  if (depot) {
    ecrire(cible, ".npmrc", "@govbf:registry=" + depot + "\n");
  }

  if (type === "statique") {
    ecrire(cible, "index.html", gabarit("statique.html", { nom: nom }));
    ecrire(cible, "LISEZMOI.md", gabarit("statique.md", { nom: nom }));
    return;
  }

  ecrire(cible, SOURCES[type], gabarit("charte.js"));
  ecrire(cible, "LISEZMOI.md", gabarit(type + ".md", { nom: nom }));
}

/* ------------------------------------------------------------- npm */

/* npm nous a lances lui-meme : il expose le chemin de son propre
   script dans npm_execpath. L'appeler par la ou nous sommes passes
   evite l'interpreteur de commandes, et donc la concatenation des
   arguments dans une ligne de commande Windows. */
function installer(cible, source) {
  const script = process.env.npm_execpath;
  if (script && /\.[cm]?js$/.test(script)) {
    execFileSync(process.execPath, [script, "install", source],
      { cwd: cible, stdio: "inherit" });
    return;
  }
  execFileSync("npm", ["install", source],
    { cwd: cible, stdio: "inherit", shell: true });
}

/* ------------------------------------------------------------ marche */

async function principal() {
  const opt = arguments_();
  if (opt.aide) return aide();

  dire("");
  dire("  " + gras("Charte graphique de l'administration burkinabè"));

  const rl = questionneur();
  try {
    if (!(await accepter(rl, opt))) {
      erreur("Conditions refusées : rien n'a été installé.");
      process.exitCode = 1;
      return;
    }

    dire("");
    const dossier = opt.dossier ||
      await demander(rl, "Dossier du projet", "mon-service");

    let type = opt.type;
    if (!type) {
      dire("");
      for (const [cle, texte] of Object.entries(TYPES)) {
        dire("    " + cle.padEnd(10) + faible(texte));
      }
      dire("");
      type = await demander(rl, "Type de projet", "statique");
    }
    if (!TYPES[type]) {
      erreur("Type inconnu : " + type + ". Attendu : " +
        Object.keys(TYPES).join(", ") + ".");
      process.exitCode = 1;
      return;
    }

    const depot = opt.depot !== null ? opt.depot
      : await demander(rl, "Dépôt npm interne, s'il y en a un", "");

    const cible = path.resolve(dossier);
    if (fs.existsSync(cible) && fs.readdirSync(cible).length) {
      erreur("Le dossier " + dossier + " existe et n'est pas vide.");
      process.exitCode = 1;
      return;
    }
    fs.mkdirSync(cible, { recursive: true });

    const nom = path.basename(cible);
    preparer(cible, type, nom, depot);

    /* Le projet naissant a besoin d'un package.json avant l'installation. */
    const manifeste = path.join(cible, "package.json");
    if (!fs.existsSync(manifeste)) {
      fs.writeFileSync(manifeste, JSON.stringify({
        name: nom.toLowerCase().replace(/[^a-z0-9-]/g, "-"),
        version: "0.1.0",
        private: true,
      }, null, 2) + "\n", "utf8");
      dire("    package.json");
    }

    dire("");
    dire("  " + gras("Installation de la charte"));
    try {
      installer(cible, process.env.FASODESIGN_SOURCE || PAQUET);
    } catch (e) {
      erreur("L'installation a échoué. Les fichiers sont écrits ; relancez\n" +
        "  « npm install " + PAQUET + " » depuis " + dossier + ".");
      process.exitCode = 1;
      return;
    }

    dire("");
    dire("  " + gras("Prêt") + "  " + faible(cible));
    dire("");
    dire("    cd " + dossier);
    dire(type === "statique"
      ? "    puis ouvrez index.html, ou servez le dossier"
      : "    puis importez " + SOURCES[type] + " depuis votre point d'entrée");
    dire("");
    dire("  " + faible("Documentation : https://chartegraphique.gov.bf"));
    dire("");
  } finally {
    rl.close();
  }
}

principal().catch((e) => {
  erreur(String(e && e.message ? e.message : e));
  process.exitCode = 1;
});
