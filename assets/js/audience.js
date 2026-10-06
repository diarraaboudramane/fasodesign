/**
 * Charte graphique de l'administration burkinabè
 * Mise à jour en direct de l'audience, au pied de l'accueil
 *
 * Outillage du site de documentation, et non comportement de la charte :
 * ce fichier n'est pas dans le paquet npm.
 *
 * Les chiffres sont redemandés toutes les 30 secondes, et seulement quand
 * l'onglet est visible : un onglet oublié en arrière-plan ne compte pas
 * comme une personne en ligne, et n'interroge pas le serveur pour rien.
 * Aucune annonce aux lecteurs d'écran : un nombre qui change toutes les
 * 30 secondes interromprait la lecture sans rien apprendre.
 */
(function () {
  "use strict";

  var bloc = document.querySelector("[data-audience]");
  if (!bloc || !window.fetch) return;

  var enLigne = bloc.querySelector("[data-audience-en-ligne]");
  var mois = bloc.querySelector("[data-audience-mois]");
  var adresse = bloc.getAttribute("data-audience");
  var minuterie = null;

  /* Comme dans la vue : en français, 0 et 1 restent au singulier. */
  function accorder(n, singulier, pluriel) {
    return n + " " + (n > 1 ? pluriel : singulier);
  }

  function rafraichir() {
    fetch(adresse, { credentials: "same-origin", headers: { Accept: "application/json" } })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (c) {
        if (!c) return;
        enLigne.textContent = accorder(c.en_ligne, "personne", "personnes") + " en ligne";
        mois.textContent = accorder(c.mois, "visiteur", "visiteurs") + " en " + c.libelle_mois;
      })
      .catch(function () { /* hors ligne : les derniers chiffres restent */ });
  }

  function demarrer() {
    if (minuterie === null) minuterie = setInterval(rafraichir, 30000);
  }

  function arreter() {
    if (minuterie !== null) clearInterval(minuterie);
    minuterie = null;
  }

  document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
      arreter();
    } else {
      rafraichir();
      demarrer();
    }
  });

  if (!document.hidden) demarrer();
})();
