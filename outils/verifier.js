#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Vérification complète — la recette, exécutable
 *
 *   npm test
 *
 * Tout ce qui pouvait être vérifié par une machine l'est ici, et rien
 * d'autre n'est affirmé ailleurs dans la documentation. C'est ce qui
 * doit passer avant chaque publication : le même script en local et en
 * intégration continue, pour qu'il n'y ait pas deux vérités.
 *
 * Aucune bibliothèque de test : la sortie se lit telle quelle.
 */
"use strict";

const fs = require("fs");
const os = require("os");
const path = require("path");
const { execFileSync } = require("child_process");
const { exporterPages } = require("./pages");

const RACINE = path.join(__dirname, "..");
let echecs = 0;

function titre(texte) {
  console.log("\n" + texte);
}

function controle(libelle, fn) {
  let detail;
  try {
    detail = fn();
  } catch (e) {
    echecs++;
    console.log("  ECHEC  " + libelle);
    console.log("         " + String(e.message).split("\n")[0]);
    return;
  }
  console.log("  ok     " + libelle + (detail ? "  — " + detail : ""));
}

function fichiers(dossier, extension) {
  const sortie = [];
  (function parcourir(d) {
    for (const e of fs.readdirSync(d, { withFileTypes: true })) {
      const p = path.join(d, e.name);
      if (e.isDirectory()) parcourir(p);
      else if (!extension || e.name.endsWith(extension)) sortie.push(p);
    }
  })(dossier);
  return sortie;
}

function lire(p) {
  return fs.readFileSync(path.join(RACINE, p), "utf8");
}

/* ============================================================ syntaxe */

titre("Syntaxe");

for (const f of ["assets/js/faso.js", "assets/js/faso-amorce.js",
  "assets/js/docs.js", "assets/js/audience.js", "outils/jetons.js", "outils/android.js",
  "outils/pages.js", "outils/site.js"]) {
  controle(f, () => {
    execFileSync(process.execPath, ["--check", path.join(RACINE, f)]);
  });
}

controle("les trois feuilles : accolades equilibrees", () => {
  for (const f of ["assets/css/tokens.css", "assets/css/faso.css", "assets/css/icones.css"]) {
    const t = lire(f);
    const o = (t.match(/\{/g) || []).length;
    const c = (t.match(/\}/g) || []).length;
    if (o !== c) throw new Error(f + " : " + o + " ouvrantes, " + c + " fermantes");
  }
  return "3 feuilles";
});

/* ============================================================== pages */

titre("Pages de documentation");

const VIDES = new Set(["meta", "link", "br", "img", "input", "hr", "source",
  "col", "area", "base", "embed", "track", "wbr"]);

function sansCommentaires(texte) {
  /* Un commentaire peut contenir des balises citees en exemple :
     les compter reviendrait a inventer des erreurs. */
  return texte.replace(/<!--[\s\S]*?-->/g, "");
}

function balises(source) {
  /* Analyse volontairement sommaire : elle ne cherche que les
     imbrications rompues, qui sont la seule erreur qu'une relecture
     humaine laisse passer. */
  const html = sansCommentaires(source);
  const pile = [];
  const motif = /<(\/?)([a-zA-Z][\w-]*)\b([^>]*)>/g;
  let m;
  while ((m = motif.exec(html)) !== null) {
    const fermant = m[1] === "/";
    const nom = m[2].toLowerCase();
    const autoFerme = /\/\s*$/.test(m[3]);
    if (VIDES.has(nom) || autoFerme) continue;

    if (!fermant) { pile.push({ nom, i: m.index }); continue; }

    if (!pile.length) throw new Error("</" + nom + "> sans ouverture");
    const haut = pile.pop();
    if (haut.nom !== nom) {
      throw new Error("attendu </" + haut.nom + ">, recu </" + nom +
        "> vers le caractere " + m.index);
    }
  }
  const reste = pile.filter((b) => b.nom !== "html" && b.nom !== "body");
  if (reste.length) throw new Error("non fermees : " + reste.map((b) => b.nom).join(", "));
}

/* Les pages sont des vues Blade : on vérifie ce que l'application en
   écrit, c'est-à-dire exactement ce que « npm run site » publie. */
const EXPORT = fs.mkdtempSync(path.join(os.tmpdir(), "fasodesign-pages-"));
process.on("exit", () => fs.rmSync(EXPORT, { recursive: true, force: true }));
let pages = [];

controle("les vues Blade s'ecrivent en HTML", () => {
  exporterPages(EXPORT);
  pages = fs.readdirSync(EXPORT).filter((f) => f.endsWith(".html"));
  if (!pages.length) throw new Error("aucune page ecrite");
  return pages.length + " pages";
});

function lirePage(p) {
  return fs.readFileSync(path.join(EXPORT, p), "utf8");
}

controle("balisage bien forme", () => {
  for (const p of pages) balises(lirePage(p));
  return pages.length + " pages";
});

controle("aucune classe orpheline", () => {
  const css = ["tokens", "faso", "icones", "docs"]
    .map((n) => lire("assets/css/" + n + ".css")).join("\n");
  const definies = new Set((css.match(/\.[A-Za-z][\w-]*/g) || []).map((c) => c.slice(1)));

  const employees = new Set();
  for (const p of pages) {
    const m = lirePage(p).match(/class="[^"]*"/g) || [];
    for (const a of m) {
      for (const c of a.slice(7, -1).split(/\s+/)) if (c) employees.add(c);
    }
  }

  const orphelines = [...employees].filter((c) => !definies.has(c));
  if (orphelines.length) throw new Error(orphelines.join(", "));
  return employees.size + " classes employees";
});

controle("etiquettes des barres synchrones", () => {
  let n = 0;
  for (const p of pages) {
    const motif = /<div class="fs-barre[^"]*" data-valeur="([\d.]+)">([\s\S]*?)<\/div>/g;
    let m;
    while ((m = motif.exec(lirePage(p))) !== null) {
      const libelle = /fs-barre-valeur">([^<]*)</.exec(m[2]);
      if (!libelle) continue;
      const texte = libelle[1].replace(/[ \s]|&nbsp;/g, "");
      const chiffre = /(\d+(?:,\d+)?)/.exec(texte);
      if (!chiffre) continue;
      n++;
      const affiche = parseFloat(chiffre[1].replace(",", "."));
      if (Math.abs(affiche - parseFloat(m[1])) > 0.05) {
        throw new Error(p + " : data-valeur=" + m[1] + ", affiche " + libelle[1]);
      }
    }
  }
  return n + " etiquettes";
});

/* ============================================================ emblemes */

titre("Emblemes et identite");

function xmlBienForme(chemin) {
  const t = sansCommentaires(fs.readFileSync(chemin, "utf8"));
  const pile = [];
  const motif = /<(\/?)([\w:.-]+)\b([^>]*?)(\/?)>/g;
  let m;
  while ((m = motif.exec(t)) !== null) {
    if (m[2].startsWith("?") || m[2].startsWith("!")) continue;
    if (m[4] === "/") continue;
    if (m[1] === "/") {
      if (pile.pop() !== m[2]) throw new Error(chemin + " : </" + m[2] + "> inattendu");
    } else {
      pile.push(m[2]);
    }
  }
  if (pile.length) throw new Error(chemin + " : " + pile.join(", ") + " non fermees");
}

controle("SVG de la charte", () => {
  const liste = fichiers(path.join(RACINE, "assets", "img"), ".svg");
  for (const f of liste) xmlBienForme(f);
  return liste.length + " fichiers";
});

controle("declinaisons de l'embleme", () => {
  /* Les PNG demandent Inkscape : on verifie qu'ils sont la, non
     qu'on saurait les reproduire ici. */
  const attendus = [
    /* les quatre formats gradues */
    "armoiries.svg", "armoiries-moyen.svg", "armoiries-ecu.svg", "favicon.svg",
    /* la version une encre */
    "armoiries-gris.svg",
    /* le matriciel */
    "armoiries-512.png", "armoiries-256.png", "armoiries-128.png",
    "armoiries-64.png", "armoiries-ecu-64.png", "armoiries-ecu-32.png",
    "favicon-32.png", "favicon-180.png"];
  const manquants = attendus.filter(
    (f) => !fs.existsSync(path.join(RACINE, "assets", "img", f)));
  if (manquants.length) throw new Error("absents : " + manquants.join(", "));

  /* Le favicon doit rester minuscule : c'est sa raison d'etre. */
  const poids = fs.statSync(path.join(RACINE, "assets", "img", "favicon.svg")).size;
  if (poids > 2048) throw new Error("favicon.svg pese " + poids + " octets");

  return attendus.length + " fichiers, favicon " + poids + " octets";
});

controle("ressources Android bien formees", () => {
  const dossier = path.join(RACINE, "android");
  if (!fs.existsSync(dossier)) throw new Error("dossier android/ absent");
  const liste = fichiers(dossier, ".xml");
  for (const f of liste) xmlBienForme(f);
  return liste.length + " fichiers XML";
});

controle("couleurs du drapeau intactes", () => {
  const t = lire("assets/css/tokens.css");
  for (const c of ["#e30613", "#00843b", "#ffed00"]) {
    if (t.indexOf(c) < 0) throw new Error("teinte " + c + " absente de tokens.css");
  }
  if (t.indexOf("--etoile-or") < 0) throw new Error("jeton --etoile-or absent");

  /* Toutes les marques nationales portent la composition du drapeau :
     rouge au-dessus, vert au-dessous, etoile centree. Une seule
     orientation est admise, et le controle la verifie marque par
     marque plutot que de se contenter de sa presence quelque part. */
  const composition = /linear-gradient\(to bottom,\s*var\(--rouge-500\) 0 50%,\s*var\(--vert-500\) 50% 100%\)/;

  const feuilles = {
    "assets/css/faso.css": [".fs-filet", ".fs-repere", ".fs-carte--marquee::before"],
    "assets/css/docs.css": [".doc-menu-marque::after", ".doc-section > h2::before"],
  };

  let marques = 0;
  for (const [feuille, attendues] of Object.entries(feuilles)) {
    const texte = lire(feuille);
    /* Chaque regle qui pose l'etoile doit poser le drapeau avec elle. */
    const blocs = texte.split("var(--etoile-or)").slice(1);
    for (const b of blocs) {
      const suite = b.slice(0, 200);
      if (!composition.test(suite)) {
        throw new Error(feuille + " : une marque ne porte pas la composition du " +
          "drapeau — " + suite.slice(0, 70).replace(/\s+/g, " ").trim());
      }
      marques++;
    }
    for (const nom of attendues) {
      if (texte.indexOf(nom) < 0) throw new Error(feuille + " : " + nom + " absente");
    }
  }

  return "rouge, vert, or, et " + marques + " marques en composition du drapeau";
});

/* =========================================================== jetons */

titre("Jetons");

for (const [libelle, outil] of [
  ["jetons.json correspond a tokens.css", "jetons.js"],
  ["ressources Android correspondent a tokens.css", "android.js"],
]) {
  controle(libelle, () => {
    execFileSync(process.execPath, [path.join(__dirname, outil), "--verifier"],
      { stdio: "pipe" });
  });
}

/* ====================================================== hebergement */

titre("Hebergement");

controle("robots.txt et les configurations correspondent", () => {
  execFileSync(process.execPath, [path.join(__dirname, "robots.js"), "--verifier"],
    { stdio: "pipe" });
});

/*
 * Le refus des collecteurs vit dans le .htaccess, que site.js depose a
 * la racine du site. Un .htaccess sans ce bloc mettrait en ligne un
 * site ouvert a tous les collecteurs, sans qu'aucune page ne change
 * d'apparence : la panne serait invisible.
 */
controle("le .htaccess publie refuse les collecteurs", () => {
  const texte = lire("hebergement/apache.htaccess");
  for (const attendu of [
    "SetEnvIfNoCase User-Agent \"GPTBot\"",
    "Require not env faso_collecteur",
  ]) {
    if (texte.indexOf(attendu) < 0) throw new Error(attendu + " absent");
  }
  /* Une reserve de fouille qui porte noindex retirerait le site des
     moteurs de recherche. La documentation doit rester trouvable. */
  if (/X-Robots-Tag[^\n]*noindex/.test(texte)) {
    throw new Error("X-Robots-Tag porte noindex : le site sortirait des " +
      "resultats de recherche");
  }
});

controle("les moteurs de recherche restent autorises", () => {
  const texte = lire("hebergement/robots.txt");
  for (const moteur of ["Googlebot", "Bingbot", "DuckDuckBot", "Applebot"]) {
    /* Applebot-Extended est refuse, Applebot ne doit pas l'etre : on
       cherche donc le nom suivi d'une fin de ligne, pas le nom seul. */
    const refuse = new RegExp("^User-agent:\\s*" + moteur + "\\s*(#|$)", "mi");
    if (refuse.test(texte)) {
      throw new Error(moteur + " est refuse : la documentation sortirait " +
        "des resultats de recherche");
    }
  }
  return "4 moteurs verifies";
});

/* =========================================================== paquet */

titre("Paquet");

controle("tous les chemins publics resolvent", () => {
  const paquet = JSON.parse(lire("package.json"));
  const manquants = [];
  for (const [chemin, cible] of Object.entries(paquet.exports)) {
    const fichier = typeof cible === "string" ? cible : cible.default;
    if (!fichier || fichier.indexOf("*") >= 0) continue;
    if (!fs.existsSync(path.join(RACINE, fichier))) manquants.push(chemin);
  }
  if (manquants.length) throw new Error("cibles absentes : " + manquants.join(", "));
  return Object.keys(paquet.exports).length + " chemins";
});

controle("les polices sont la ou le CSS les cherche", () => {
  const css = lire("assets/css/tokens.css");
  const urls = [...css.matchAll(/url\("([^"]+woff2)"\)/g)].map((m) => m[1]);
  if (!urls.length) throw new Error("aucune police declaree");
  for (const u of urls) {
    const p = path.join(RACINE, "assets", "css", u);
    if (!fs.existsSync(p)) throw new Error("introuvable : " + u);
  }
  return urls.length + " fichiers";
});

controle("une page npm existe", () => {
  if (!fs.existsSync(path.join(RACINE, "README.md"))) {
    throw new Error("README.md absent : la fiche du paquet serait vide");
  }
});

/* ====================================================== publication */

/*
 * Ces controles ne s'executent qu'avant une publication :
 *
 *   node outils/verifier.js --publication
 *
 * Ils ne concernent pas le code mais les decisions qui doivent avoir
 * ete prises pour qu'un paquet de l'Etat parte sur un registre public.
 * Les laisser dans la suite ordinaire la ferait echouer chaque jour
 * pour une raison qui n'est pas technique.
 */
if (process.argv.includes("--publication")) {
  titre("Avant publication");

  for (const manifeste of ["package.json", "amorce/package.json"]) {
    controle("aucun gabarit a renseigner dans " + manifeste, () => {
      const brut = lire(manifeste);
      if (brut.indexOf("A-RENSEIGNER") >= 0) {
        const lignes = brut.split("\n")
          .filter((l) => l.indexOf("A-RENSEIGNER") >= 0)
          .map((l) => l.trim());
        throw new Error(lignes.join(" | "));
      }
    });
  }

  controle("conditions de reutilisation arretees", () => {
    const texte = lire("LISEZMOI-PAQUET.txt");
    if (/reste\s+a\s+d[eé]signer|a\s+arr[eê]ter|proposition/i.test(texte)) {
      throw new Error("LISEZMOI-PAQUET.txt annonce encore des conditions " +
        "provisoires : l'autorite de tutelle doit avoir tranche");
    }
  });

  controle("la version n'est pas deja publiee", () => {
    const paquet = JSON.parse(lire("package.json"));
    let deja;
    try {
      deja = execFileSync("npm", ["view", paquet.name + "@" + paquet.version,
        "version"], { stdio: "pipe", encoding: "utf8", shell: true }).trim();
    } catch (e) {
      return "version " + paquet.version + ", absente du registre";
    }
    if (deja) throw new Error(paquet.name + "@" + deja + " existe deja");
  });

  controle("l'archive ne contient que ce qui est declare", () => {
    const sortie = execFileSync("npm", ["pack", "--dry-run", "--json",
      "--ignore-scripts"], { cwd: RACINE, stdio: "pipe", encoding: "utf8", shell: true });
    const archive = JSON.parse(sortie)[0];

    const indesirables = archive.files
      .map((f) => f.path)
      .filter((p) => /^(tests|outils|docs?)\//.test(p) || /\.(map|log|tgz)$/.test(p));

    if (indesirables.length) throw new Error(indesirables.join(", "));
    return archive.entryCount + " fichiers, " +
      Math.round(archive.size / 1024) + " Ko";
  });
}

/* ============================================================ essais */

titre("Comportements");

for (const essai of fs.readdirSync(path.join(RACINE, "tests")).sort()) {
  if (!essai.endsWith(".js")) continue;
  controle(essai.replace(/\.js$/, ""), () => {
    execFileSync(process.execPath, [path.join(RACINE, "tests", essai)], { stdio: "pipe" });
  });
}

/* ================================================= application Laravel */

titre("Application");

controle("essais de l'application (phpunit)", () => {
  const php = process.env.PHP || "php";
  const phpunit = path.join(RACINE, "vendor", "phpunit", "phpunit", "phpunit");
  if (!fs.existsSync(phpunit)) {
    throw new Error("phpunit absent : lancer « composer install » a la racine du projet");
  }
  let sortie;
  try {
    sortie = execFileSync(php, [phpunit, "--colors=never"],
      { cwd: RACINE, stdio: "pipe", encoding: "utf8" });
  } catch (e) {
    const lignes = String(e.stdout || e.message).split("\n")
      .filter((l) => /^\d+\)|FAILURES|Tests:/.test(l));
    throw new Error(lignes.join(" | ") || "echec");
  }
  const bilan = /OK \((\d+) tests?, (\d+) assertions?\)/.exec(sortie);
  return bilan ? bilan[1] + " essais, " + bilan[2] + " assertions" : "";
});

/* ============================================================= bilan */

console.log("");
if (echecs) {
  console.log("  " + echecs + " controle(s) en echec.");
  process.exit(1);
}
console.log("  Tout est verifie.");
