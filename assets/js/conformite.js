/**
 * Outil de contrôle de conformité : le comportement de ses pages.
 *
 *   - le bouton qui imprime le bilan, ou l'enregistre en PDF ;
 *   - le formulaire d'analyse, qui n'affiche que les champs du mode choisi ;
 *   - l'envoi de l'archive avec sa progression réelle, puis le sablier
 *     pendant l'analyse, et la possibilité d'annuler l'envoi.
 *
 * Sans ce script, tout fonctionne encore : les deux blocs restent visibles
 * et le formulaire part comme un formulaire ordinaire.
 *
 * Outillage du site, et non comportement de la charte : ce fichier n'est
 * pas dans le paquet npm.
 */
(function () {
  "use strict";

  /* ------------------------------------------------------ impression */

  document.addEventListener("click", function (evenement) {
    var bouton = evenement.target.closest && evenement.target.closest("[data-imprimer]");
    if (bouton) window.print();
  });

  /* ------------------------------------------- formulaire d'analyse */

  var formulaire = document.querySelector("form[data-lancement]");
  if (!formulaire) return;

  var panneau = formulaire.querySelector("[data-progression]");
  var titre = panneau.querySelector("[data-progression-titre]");
  var envoi = panneau.querySelector("[data-progression-envoi]");
  var texte = panneau.querySelector("[data-progression-texte]");
  var pourcent = panneau.querySelector("[data-progression-pourcent]");
  var jauge = panneau.querySelector(".fs-jauge");
  var barre = panneau.querySelector(".fs-jauge-valeur");
  var analyse = panneau.querySelector("[data-progression-analyse]");
  var analyseTexte = panneau.querySelector("[data-progression-analyse-texte]");
  var annuler = panneau.querySelector("[data-progression-annuler]");
  var lancer = formulaire.querySelector("[data-lancer]");
  var requete = null;
  var minuterie = null;

  function mode() {
    var coche = formulaire.querySelector("input[name=mode]:checked");
    return coche ? coche.value : "adresse";
  }

  /* Seuls les champs du mode choisi restent visibles. */
  function afficherMode() {
    formulaire.querySelectorAll("[data-mode-bloc]").forEach(function (bloc) {
      bloc.hidden = bloc.getAttribute("data-mode-bloc") !== mode();
    });
  }
  formulaire.addEventListener("change", function (e) {
    if (e.target.name === "mode") afficherMode();
  });
  afficherMode();

  function mo(octets) {
    return (octets / 1048576).toFixed(octets < 10485760 ? 1 : 0).replace(".", ",") + " Mo";
  }

  /* Le temps écoulé rassure pendant une attente longue : l'usager sait que
     rien n'est figé, et combien de temps il attend déjà. */
  function demarrerAnalyse(libelle) {
    envoi.hidden = mode() !== "archive";
    analyse.hidden = false;
    annuler.hidden = true;
    titre.textContent = "Analyse en cours";
    var debut = Date.now();
    clearInterval(minuterie);
    minuterie = setInterval(function () {
      var s = Math.round((Date.now() - debut) / 1000);
      analyseTexte.textContent = libelle + " — " + s + " s écoulée" + (s > 1 ? "s" : "") + ".";
    }, 1000);
    analyseTexte.textContent = libelle + ".";
  }

  function occuper(occupe) {
    lancer.disabled = occupe;
    formulaire.setAttribute("aria-busy", occupe ? "true" : "false");
    panneau.hidden = !occupe;
    if (!occupe) {
      clearInterval(minuterie);
      requete = null;
    }
  }

  function progression(charge, total) {
    var p = total ? Math.min(100, Math.round(charge / total * 100)) : 0;
    barre.style.width = p + "%";
    jauge.setAttribute("aria-valuenow", String(p));
    pourcent.textContent = p + " %";
    texte.textContent = mo(charge) + " envoyés sur " + mo(total);
  }

  /* Les erreurs rendues par le serveur, dans le récapitulatif de la charte. */
  function afficherErreurs(erreurs) {
    var recap = document.querySelector(".fs-recap-erreurs");
    if (!recap) {
      recap = document.createElement("div");
      recap.className = "fs-recap-erreurs";
      recap.setAttribute("role", "alert");
      recap.tabIndex = -1;
      formulaire.parentNode.insertBefore(recap, formulaire);
    }
    var champs = Object.keys(erreurs);
    recap.innerHTML = "";
    var h = document.createElement("h2");
    h.textContent = champs.length === 1 ? "Une information doit être corrigée" : champs.length + " informations doivent être corrigées";
    var liste = document.createElement("ul");
    champs.forEach(function (champ) {
      var li = document.createElement("li");
      var a = document.createElement("a");
      a.href = "#" + champ;
      a.textContent = erreurs[champ][0];
      li.appendChild(a);
      liste.appendChild(li);
      var bloc = formulaire.querySelector("[name=" + champ + "]");
      var cadre = bloc && bloc.closest(".fs-champ");
      if (cadre) cadre.setAttribute("data-etat", "erreur");
    });
    recap.appendChild(h);
    recap.appendChild(liste);
    recap.focus();
  }

  formulaire.addEventListener("submit", function (evenement) {
    var fichier = formulaire.querySelector("input[name=archive]");

    /* Par adresse, le formulaire part normalement : on montre seulement le
       sablier et on bloque un second envoi. */
    if (mode() !== "archive" || !window.FormData || !fichier || !fichier.files.length) {
      if (mode() !== "archive") {
        occuper(true);
        demarrerAnalyse("Lecture du site et application des règles de la charte");
      }
      return;
    }

    /* Par archive, l'envoi passe par le script pour suivre sa progression. */
    evenement.preventDefault();
    occuper(true);
    envoi.hidden = false;
    analyse.hidden = true;
    annuler.hidden = false;
    titre.textContent = "Envoi de l'archive";
    progression(0, fichier.files[0].size);

    requete = new XMLHttpRequest();
    requete.open("POST", formulaire.action);
    requete.setRequestHeader("Accept", "application/json");
    requete.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    requete.upload.addEventListener("progress", function (e) {
      if (e.lengthComputable) progression(e.loaded, e.total);
    });
    /* L'archive est arrivée : le serveur l'analyse avant de répondre. */
    requete.upload.addEventListener("load", function () {
      progression(fichier.files[0].size, fichier.files[0].size);
      demarrerAnalyse("Lecture des fichiers et application des règles de la charte");
    });

    requete.addEventListener("load", function () {
      var reponse = {};
      try { reponse = JSON.parse(requete.responseText); } catch (e) { /* réponse non JSON */ }

      if (requete.status >= 200 && requete.status < 300 && reponse.adresse) {
        window.location.assign(reponse.adresse);
        return;
      }
      occuper(false);
      if (requete.status === 422 && reponse.errors) {
        afficherErreurs(reponse.errors);
      } else if (requete.status === 419) {
        afficherErreurs({ archive: ["La page a expiré. Rechargez-la, puis relancez l'analyse."] });
      } else if (requete.status === 413) {
        afficherErreurs({ archive: ["L'archive est trop lourde pour le serveur."] });
      } else {
        afficherErreurs({ archive: ["L'envoi n'a pas abouti (erreur " + requete.status + "). Réessayez dans un instant."] });
      }
    });
    requete.addEventListener("error", function () {
      occuper(false);
      afficherErreurs({ archive: ["La connexion a été interrompue pendant l'envoi. Vérifiez votre réseau, puis relancez l'analyse."] });
    });

    requete.send(new FormData(formulaire));
  });

  /* Annuler interrompt vraiment l'envoi, et rend la main. */
  annuler.addEventListener("click", function () {
    if (requete) requete.abort();
    occuper(false);
    titre.textContent = "Envoi de l'archive";
  });

  /* Retour arrière depuis le bilan : la page revient de son cache, figée
     dans l'état « en cours ». On la remet au repos. */
  window.addEventListener("pageshow", function (e) {
    if (e.persisted) occuper(false);
  });
})();
