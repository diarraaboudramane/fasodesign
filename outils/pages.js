/**
 * Charte graphique de l'administration burkinabè
 * Les pages de la documentation, écrites en HTML par l'application
 *
 * Les pages sont des vues Blade ; « php artisan charte:exporter » les
 * écrit en fichiers statiques. La construction du site et la
 * vérification passent toutes deux par ici, pour que ce qui est vérifié
 * soit exactement ce qui est publié.
 */
"use strict";

const fs = require("fs");
const path = require("path");
const { execFileSync } = require("child_process");

const RACINE = path.join(__dirname, "..");

function exporterPages(sortie) {
  if (!fs.existsSync(path.join(RACINE, "vendor", "autoload.php"))) {
    throw new Error("dependances PHP absentes : lancer « composer install » " +
      "a la racine du projet");
  }
  const php = process.env.PHP || "php";
  try {
    execFileSync(php, [path.join(RACINE, "artisan"), "charte:exporter", sortie],
      { cwd: RACINE, stdio: "pipe", encoding: "utf8" });
  } catch (e) {
    const detail = (e.stderr || e.stdout || e.message || "").trim().split("\n")[0];
    throw new Error("export des pages impossible (" + php + ") : " + detail);
  }
}

module.exports = { exporterPages };
