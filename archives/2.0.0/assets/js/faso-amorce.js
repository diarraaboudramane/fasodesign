/**
 * Charte graphique de l'administration burkinabè
 * Amorce du thème — à placer dans <head>, avant la feuille de style.
 *
 * Le thème est posé sur <html> avant le premier rendu. Sans cela,
 * une page réglée en sombre s'affiche brièvement en clair, le temps
 * que faso.js s'exécute : le clignotement est visible et désagréable.
 *
 * Ce fichier ne dépend de rien, n'expose rien et ne fait rien
 * d'autre. Il pèse moins que la ligne de script qu'il remplace, et
 * évite d'avoir à autoriser 'unsafe-inline' dans la politique de
 * sécurité du contenu.
 */
(function () {
  "use strict";
  try {
    var choix = localStorage.getItem("faso-theme");
    if (choix === "dark" || choix === "light") {
      document.documentElement.setAttribute("data-theme", choix);
    }
  } catch (e) {
    /* Stockage indisponible : navigation privée, site bloqué.
       Le thème clair, qui est le défaut, s'applique. */
  }
})();
