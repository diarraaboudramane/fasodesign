/* =============================================================
   Charte graphique de l'administration burkinabè
   faso.js — comportements des composants
   Version 2.0

   Principes retenus :
   - aucune dépendance externe ;
   - tout est piloté par attribut data-*, jamais par classe, afin
     que le style et le comportement restent découplés ;
   - chaque composant reste utilisable sans JavaScript lorsque
     c'est techniquement possible (accordéon, onglets en ancres) ;
   - l'état accessible (aria-*) est la source de vérité, pas une
     décoration ajoutée après coup.
   ============================================================= */

(function () {
  "use strict";

  var FOCUSABLES = [
    'a[href]', 'button:not([disabled])', 'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])', 'textarea:not([disabled])', '[tabindex]:not([tabindex="-1"])'
  ].join(',');

  function $(sel, racine) { return (racine || document).querySelector(sel); }
  function $$(sel, racine) { return Array.prototype.slice.call((racine || document).querySelectorAll(sel)); }

  /* -----------------------------------------------------------
     Environnement

     Le fichier doit pouvoir être importé là où il n'y a pas de
     DOM : rendu côté serveur d'Angular, de Next, test unitaire en
     Node. Rien ne s'exécute alors au chargement, et l'API reste
     appelable sans effet.
     ----------------------------------------------------------- */
  var NAVIGATEUR = typeof window !== "undefined" && typeof document !== "undefined";

  /* -----------------------------------------------------------
     Registre des éléments déjà initialisés

     Un cadriciel rend, démonte et re-rend. Sans garde, rappeler
     l'initialisation empilerait les gestionnaires : une pagination
     cliquée une fois changerait deux fois de page.

     Une WeakMap plutôt qu'un attribut : elle ne salit pas le
     balisage, ne provoque aucun écart d'hydratation entre serveur
     et navigateur, et libère les nœuds retirés du document.
     ----------------------------------------------------------- */
  var PRETS = NAVIGATEUR ? new WeakMap() : null;

  function nouveau(element, cle) {
    if (!PRETS) return false;
    var vus = PRETS.get(element);
    if (!vus) { vus = {}; PRETS.set(element, vus); }
    if (vus[cle]) return false;
    vus[cle] = true;
    return true;
  }

  /* -----------------------------------------------------------
     Résolution d'une cible par identifiant

     Un cadriciel qui rend le même composant plusieurs fois produit
     autant d'éléments portant le même identifiant. Le document n'en
     connaît alors qu'un, le premier : le bouton de la dixième ligne
     ouvrirait la modale de la première, sans qu'aucune erreur ne le
     signale.

     La recherche part donc du déclencheur et remonte : le premier
     ancêtre qui contient un élément portant cet identifiant gagne.
     L'instance la plus proche l'emporte, ce qui est toujours celle
     que l'usager désigne. Lorsque l'identifiant est unique — le cas
     ordinaire — le résultat est exactement celui de getElementById.
     ----------------------------------------------------------- */
  function echapper(valeur) {
    if (window.CSS && CSS.escape) return CSS.escape(valeur);
    return valeur.replace(/["\\]/g, "\\$&");
  }

  function resoudre(depuis, identifiant) {
    if (!identifiant) return null;

    var selecteur = '[id="' + echapper(identifiant) + '"]';
    var arbre = depuis && depuis.getRootNode ? depuis.getRootNode() : document;
    if (!arbre.querySelectorAll) arbre = document;

    /* Cas ordinaire : l'identifiant ne désigne qu'un élément. Le
       résultat est alors exactement celui de getElementById, et la
       remontée ci-dessous ne s'exécute jamais. */
    var candidats = arbre.querySelectorAll(selecteur);
    if (candidats.length === 1) return candidats[0];
    if (!candidats.length) {
      return arbre === document ? null : document.getElementById(identifiant);
    }

    /* Plusieurs porteurs du même identifiant : on remonte depuis le
       déclencheur, en franchissant au besoin la frontière d'une
       racine fantôme. L'instance la plus proche l'emporte. */
    var noeud = depuis;
    while (noeud) {
      var parent = noeud.parentNode;
      if (!parent) break;
      var trouve = parent.querySelector ? parent.querySelector(selecteur) : null;
      if (trouve) return trouve;
      noeud = parent.host || parent;
    }
    return candidats[0];
  }

  /* Un événement parti d'une racine fantôme ouverte traverse bien le
     document, mais sa cible est recalée sur l'hôte : la délégation
     ne verrait plus le bouton réellement actionné. Le chemin composé
     rend l'élément d'origine. */
  function cibleDe(ev) {
    if (ev.composedPath) {
      var chemin = ev.composedPath();
      if (chemin.length && chemin[0] && chemin[0].closest) return chemin[0];
    }
    return ev.target;
  }

  /* Un cadriciel peut reprendre à son compte l'écriture d'un état.
     Chaque changement est annoncé par un événement annulable :
     preventDefault() laisse la charte assurer le clavier, le focus
     et l'annonce, sans toucher aux attributs que le projet lie. */
  function avis(nom, detail, annulable) {
    return new CustomEvent(nom, {
      bubbles: true, cancelable: !!annulable, detail: detail || null
    });
  }

  /* Chaque annonce porte deux noms. Les gabarits d'Angular
     n'acceptent pas les deux-points dans une liaison d'événement :
     (fs:onglet) y désignerait la cible « fs » et le modèle serait
     refusé à la compilation. La forme à trait d'union se lie
     directement, (fs-onglet)="...", et la forme à deux-points reste
     celle que lisent les autres cadriciels et le JavaScript nu. */
  function diffuser(element, nom, detail) {
    if (!element) return;
    element.dispatchEvent(avis("fs:" + nom, detail, false));
    element.dispatchEvent(avis("fs-" + nom, detail, false));
  }

  function repris(element, nom, detail) {
    if (!element) return false;
    /* Les deux formes sont émises quoi qu'il arrive : annuler l'une
       suffit, et le projet n'a pas à savoir laquelle la charte
       consulte en premier. */
    var deuxPoints = element.dispatchEvent(avis("fs:" + nom, detail, true));
    var trait = element.dispatchEvent(avis("fs-" + nom, detail, true));
    return !deuxPoints || !trait;
  }

  /* Suit une valeur portée par un attribut, y compris quand le
     cadriciel remplace les nœuds qui la portent. */
  function suivreValeurs(element, noms, rappel) {
    if (!NAVIGATEUR || !window.MutationObserver) return;
    new MutationObserver(rappel).observe(element, {
      attributes: true, attributeFilter: noms, childList: true, subtree: true
    });
  }

  /* Le verrou de défilement est compté : deux couches superposées le
     posent deux fois et ne le lèvent qu'à la fermeture de la
     dernière. La valeur d'origine est rendue telle quelle. */
  var VERROUS = 0;
  var DEFILEMENT_INITIAL = "";

  function verrouillerDefilement() {
    if (VERROUS === 0) DEFILEMENT_INITIAL = document.body.style.overflow;
    VERROUS++;
    document.body.style.overflow = "hidden";
  }

  function libererDefilement() {
    if (VERROUS === 0) return;
    VERROUS--;
    if (VERROUS === 0) document.body.style.overflow = DEFILEMENT_INITIAL;
  }

  /* Le champ vient d'être modifié par le script et non par la
     frappe : les cadriciels n'en savent rien. Sans ces deux
     événements, une liaison bidirectionnelle conserve l'ancienne
     valeur et le formulaire part incomplet.

     Le drapeau distingue cette écriture d'une frappe réelle. Nos
     propres écouteurs de saisie s'en servent pour ne pas se
     réveiller : sans lui, retenir une suggestion rouvrirait la
     liste que l'on vient de fermer. La distribution étant
     synchrone, le drapeau retombe avant tout autre traitement. */
  var ECRITURE_SCRIPT = false;

  function signalerSaisie(champ) {
    ECRITURE_SCRIPT = true;
    champ.dispatchEvent(new Event("input", { bubbles: true }));
    champ.dispatchEvent(new Event("change", { bubbles: true }));
    ECRITURE_SCRIPT = false;
  }

  /* -----------------------------------------------------------
     Thème

     Le choix explicite prime sur la préférence système et est
     conservé. L'accès au stockage est protégé : en navigation
     privée ou site bloqué, la lecture peut lever une exception.
     ----------------------------------------------------------- */
  var CLE_THEME = "faso-theme";

  function lireTheme() {
    try { return localStorage.getItem(CLE_THEME); } catch (e) { return null; }
  }
  function ecrireTheme(valeur) {
    try { localStorage.setItem(CLE_THEME, valeur); } catch (e) { /* stockage indisponible */ }
  }

  function appliquerTheme(valeur) {
    if (valeur) {
      document.documentElement.setAttribute("data-theme", valeur);
    } else {
      document.documentElement.removeAttribute("data-theme");
    }
    $$('[data-bascule-theme]').forEach(function (btn) {
      var sombre = document.documentElement.getAttribute("data-theme") === "dark";
      btn.setAttribute("aria-pressed", String(sombre));
      var libelle = btn.querySelector("[data-theme-libelle]");
      if (libelle) libelle.textContent = sombre ? "Thème clair" : "Thème sombre";
    });
  }

  function initTheme() {
    appliquerTheme(lireTheme());

    document.addEventListener("click", function (ev) {
      var btn = cibleDe(ev).closest("[data-bascule-theme]");
      if (!btn) return;
      /* Le thème clair est le défaut du système : en l'absence
         d'attribut, l'état courant est clair, quelle que soit la
         préférence du système d'exploitation. */
      var actuel = document.documentElement.getAttribute("data-theme") || "light";
      var suivant = actuel === "dark" ? "light" : "dark";
      ecrireTheme(suivant);
      appliquerTheme(suivant);
    });
  }

  /* -----------------------------------------------------------
     Navigation repliable
     ----------------------------------------------------------- */
  function initNavigation() {
    document.addEventListener("click", function (ev) {
      var btn = cibleDe(ev).closest("[data-bascule]");
      if (!btn) return;

      var cible = resoudre(btn, btn.getAttribute("data-bascule"));
      if (!cible) return;

      var ouvert = cible.getAttribute("data-ouvert") === "true";
      if (repris(cible, "bascule", { ouvert: !ouvert })) return;
      cible.setAttribute("data-ouvert", String(!ouvert));
      btn.setAttribute("aria-expanded", String(!ouvert));
    });
  }

  /* -----------------------------------------------------------
     Onglets

     Le clavier suit la convention ARIA : flèches pour changer
     d'onglet, Début et Fin pour aller aux extrémités.
     ----------------------------------------------------------- */
  function initOnglets(racine) {
    $$('[role="tablist"]', racine).forEach(function (liste) {
      if (!nouveau(liste, "onglets")) return;
      var onglets = $$('[role="tab"]', liste);
      if (!onglets.length) return;

      function activer(onglet, donnerFocus) {
        /* Le projet qui lie lui-même [hidden] ou [attr.aria-selected]
           annule l'annonce : la charte s'en tient alors au clavier,
           au focus et à l'ordre de tabulation, qui ne se lient pas. */
        var externe = repris(liste, "onglet", {
          onglet: onglet, index: onglets.indexOf(onglet)
        });

        onglets.forEach(function (o) {
          var actif = o === onglet;
          o.tabIndex = actif ? 0 : -1;
          if (externe) return;
          o.setAttribute("aria-selected", String(actif));
          var panneau = resoudre(o, o.getAttribute("aria-controls"));
          if (panneau) panneau.hidden = !actif;
        });
        if (donnerFocus) onglet.focus();
      }

      onglets.forEach(function (onglet, index) {
        onglet.addEventListener("click", function () { activer(onglet, false); });

        onglet.addEventListener("keydown", function (ev) {
          var suivant = null;
          if (ev.key === "ArrowRight") suivant = onglets[(index + 1) % onglets.length];
          else if (ev.key === "ArrowLeft") suivant = onglets[(index - 1 + onglets.length) % onglets.length];
          else if (ev.key === "Home") suivant = onglets[0];
          else if (ev.key === "End") suivant = onglets[onglets.length - 1];
          if (!suivant) return;
          ev.preventDefault();
          activer(suivant, true);
        });
      });

      var courant = onglets.filter(function (o) { return o.getAttribute("aria-selected") === "true"; })[0];
      activer(courant || onglets[0], false);
    });
  }

  /* -----------------------------------------------------------
     Modales

     S'appuie sur <dialog>. Le retour du focus sur l'élément
     déclencheur est géré explicitement : sans cela, l'usager au
     clavier est renvoyé en début de document à la fermeture.
     ----------------------------------------------------------- */
  var declencheurModale = null;

  function initModales() {
    document.addEventListener("click", function (ev) {
      var ouvrir = cibleDe(ev).closest("[data-ouvre-modale]");
      if (ouvrir) {
        var modale = resoudre(ouvrir, ouvrir.getAttribute("data-ouvre-modale"));
        if (modale && typeof modale.showModal === "function") {
          declencheurModale = ouvrir;
          modale.showModal();
        }
        return;
      }

      var fermer = cibleDe(ev).closest("[data-ferme-modale]");
      if (fermer) {
        var parente = fermer.closest("dialog");
        if (parente) parente.close();
      }
    });
  }

  /* Les deux gestionnaires ci-dessous ne peuvent pas être délégués :
     ils portent sur la boîte de dialogue elle-même. Ils sont donc
     posés par élément, et gardés contre la double liaison. */
  function initModalesLocales(racine) {
    $$("dialog.fs-modale", racine).forEach(function (modale) {
      if (!nouveau(modale, "modale")) return;

      /* Clic sur le fond : <dialog> ne distingue pas le fond du
         contenu, on compare donc les coordonnées à la boîte. */
      modale.addEventListener("click", function (ev) {
        if (ev.target !== modale) return;
        var boite = modale.getBoundingClientRect();
        var dedans = ev.clientX >= boite.left && ev.clientX <= boite.right &&
                     ev.clientY >= boite.top && ev.clientY <= boite.bottom;
        if (!dedans) modale.close();
      });

      modale.addEventListener("close", function () {
        if (declencheurModale && document.contains(declencheurModale)) {
          declencheurModale.focus();
        }
        declencheurModale = null;
      });
    });
  }

  /* -----------------------------------------------------------
     Panneau latéral

     Contrairement à <dialog>, il faut enfermer le focus à la
     main et rétablir le défilement du document.
     ----------------------------------------------------------- */
  function initPanneaux() {
    /* Une pile, et non un panneau unique : un panneau peut en ouvrir
       un second — choisir une commune, un service — et la fermeture
       doit rendre le premier exactement tel qu'il était. */
    var pile = [];

    function sommet() { return pile.length ? pile[pile.length - 1] : null; }

    function voile(ouvert) {
      var v = $(".fs-voile");
      if (v) v.setAttribute("data-ouvert", ouvert ? "true" : "false");
    }

    /* Un cadriciel peut détruire un panneau ouvert sans passer par la
       fermeture. Sans ce balayage, le verrou de défilement resterait
       posé et la page ne défilerait plus. */
    function purger() {
      for (var i = pile.length - 1; i >= 0; i--) {
        if (!document.contains(pile[i].panneau)) {
          pile.splice(i, 1);
          libererDefilement();
        }
      }
      voile(pile.length > 0);
    }

    function ouvrir(panneau, declencheur) {
      purger();
      if (!panneau) return;
      var deja = pile.some(function (e) { return e.panneau === panneau; });
      if (deja) return;

      pile.push({ panneau: panneau, declencheur: declencheur });
      panneau.setAttribute("data-ouvert", "true");
      panneau.setAttribute("aria-hidden", "false");
      voile(true);
      verrouillerDefilement();

      var premier = $(FOCUSABLES, panneau);
      if (premier) premier.focus();
    }

    function fermer() {
      purger();
      var entree = pile.pop();
      if (!entree) return;

      entree.panneau.setAttribute("data-ouvert", "false");
      entree.panneau.setAttribute("aria-hidden", "true");
      libererDefilement();
      voile(pile.length > 0);

      if (entree.declencheur && document.contains(entree.declencheur)) {
        entree.declencheur.focus();
      } else if (pile.length) {
        var dessous = $(FOCUSABLES, sommet().panneau);
        if (dessous) dessous.focus();
      }
    }

    document.addEventListener("click", function (ev) {
      var cible = cibleDe(ev);

      var btn = cible.closest("[data-ouvre-panneau]");
      if (btn) {
        ouvrir(resoudre(btn, btn.getAttribute("data-ouvre-panneau")), btn);
        return;
      }

      if (cible.closest("[data-ferme-panneau]") ||
          (cible.classList && cible.classList.contains("fs-voile"))) {
        fermer();
      }
    });

    document.addEventListener("keydown", function (ev) {
      if (!pile.length) return;
      if (ev.key !== "Escape" && ev.key !== "Tab") return;

      purger();
      var courant = sommet();
      if (!courant) return;

      if (ev.key === "Escape") { fermer(); return; }

      var cibles = $$(FOCUSABLES, courant.panneau);
      if (!cibles.length) return;
      var premier = cibles[0];
      var dernier = cibles[cibles.length - 1];

      if (ev.shiftKey && document.activeElement === premier) {
        ev.preventDefault();
        dernier.focus();
      } else if (!ev.shiftKey && document.activeElement === dernier) {
        ev.preventDefault();
        premier.focus();
      }
    });
  }

  /* -----------------------------------------------------------
     Menus déroulants
     ----------------------------------------------------------- */
  function initMenus() {
    function toutFermer(sauf) {
      $$("[data-menu]").forEach(function (menu) {
        if (menu === sauf) return;
        var liste = $(".fs-menu-liste", menu);
        var btn = $("[aria-haspopup]", menu);
        if (liste) liste.hidden = true;
        if (btn) btn.setAttribute("aria-expanded", "false");
      });
    }

    document.addEventListener("click", function (ev) {
      var btn = cibleDe(ev).closest("[data-menu] [aria-haspopup]");
      if (btn) {
        var menu = btn.closest("[data-menu]");
        var liste = $(".fs-menu-liste", menu);
        var ouvert = !liste.hidden;
        toutFermer(menu);
        liste.hidden = ouvert;
        btn.setAttribute("aria-expanded", String(!ouvert));
        if (!ouvert) {
          var premier = $(FOCUSABLES, liste);
          if (premier) premier.focus();
        }
        return;
      }
      toutFermer(null);
    });

    document.addEventListener("keydown", function (ev) {
      if (ev.key !== "Escape") return;
      var menu = document.activeElement && document.activeElement.closest("[data-menu]");
      toutFermer(null);
      if (menu) {
        var btn = $("[aria-haspopup]", menu);
        if (btn) btn.focus();
      }
    });
  }

  /* -----------------------------------------------------------
     Notifications passagères

     API : Faso.notifier({ titre, texte, ton, duree })
     Le conteneur est une région live polie : le message est
     annoncé sans interrompre la tâche en cours.
     ----------------------------------------------------------- */
  function conteneurToasts() {
    var zone = $(".fs-toasts");
    if (zone) return zone;

    zone = document.createElement("div");
    zone.className = "fs-toasts";
    zone.setAttribute("role", "status");
    zone.setAttribute("aria-live", "polite");
    document.body.appendChild(zone);
    return zone;
  }

  function notifier(options) {
    /* Appelée depuis un rendu côté serveur, la fonction ne fait rien
       et rend une fermeture inerte : le code appelant n'a pas à
       savoir où il s'exécute. */
    if (!NAVIGATEUR) return function () {};

    options = options || {};
    var zone = conteneurToasts();

    var toast = document.createElement("div");
    toast.className = "fs-toast" + (options.ton ? " fs-toast--" + options.ton : "");

    var corps = document.createElement("div");
    corps.className = "fs-toast-corps";

    if (options.titre) {
      var titre = document.createElement("div");
      titre.className = "fs-toast-titre";
      titre.textContent = options.titre;
      corps.appendChild(titre);
    }
    if (options.texte) {
      var texte = document.createElement("div");
      texte.textContent = options.texte;
      corps.appendChild(texte);
    }

    var fermer = document.createElement("button");
    fermer.className = "fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone";
    fermer.type = "button";
    fermer.setAttribute("aria-label", "Fermer la notification");
    fermer.textContent = "×";

    toast.appendChild(corps);
    toast.appendChild(fermer);
    zone.appendChild(toast);

    var minuteur;
    function retirer() {
      clearTimeout(minuteur);
      toast.setAttribute("data-sortie", "true");
      toast.addEventListener("animationend", function () { toast.remove(); }, { once: true });
      /* Filet de sécurité si l'animation est désactivée. */
      setTimeout(function () { if (toast.parentNode) toast.remove(); }, 400);
    }

    fermer.addEventListener("click", retirer);

    var duree = typeof options.duree === "number" ? options.duree : 6000;
    if (duree > 0) minuteur = setTimeout(retirer, duree);

    return retirer;
  }

  /* -----------------------------------------------------------
     Dépôt de fichiers
     ----------------------------------------------------------- */
  function initDepots(racine) {
    $$(".fs-depot", racine).forEach(function (depot) {
      if (!nouveau(depot, "depots")) return;
      var champ = $('input[type="file"]', depot);
      if (!champ) return;

      /* Le clic est laissé au navigateur : la zone est un <label>
         qui active nativement son champ. Le doubler en script
         ouvrirait deux fois le sélecteur de fichiers. Seul le
         clavier a besoin d'aide, un label n'étant pas activable
         par Entrée ou Espace. */
      depot.addEventListener("keydown", function (ev) {
        if (ev.key === "Enter" || ev.key === " ") {
          ev.preventDefault();
          champ.click();
        }
      });

      ["dragenter", "dragover"].forEach(function (type) {
        depot.addEventListener(type, function (ev) {
          ev.preventDefault();
          depot.setAttribute("data-survol", "true");
        });
      });

      ["dragleave", "drop"].forEach(function (type) {
        depot.addEventListener(type, function (ev) {
          ev.preventDefault();
          depot.setAttribute("data-survol", "false");
        });
      });

      depot.addEventListener("drop", function (ev) {
        if (ev.dataTransfer && ev.dataTransfer.files.length) {
          champ.files = ev.dataTransfer.files;
          signalerSaisie(champ);
        }
      });
    });
  }

  /* -----------------------------------------------------------
     Saisie de code à usage unique
     ----------------------------------------------------------- */
  function initCodes(racine) {
    $$(".fs-code", racine).forEach(function (bloc) {
      if (!nouveau(bloc, "codes")) return;
      var cases = $$("input", bloc);

      cases.forEach(function (champ, index) {
        champ.addEventListener("input", function () {
          if (ECRITURE_SCRIPT) return;
          champ.value = champ.value.replace(/\D/g, "").slice(-1);
          if (champ.value && cases[index + 1]) cases[index + 1].focus();
        });

        champ.addEventListener("keydown", function (ev) {
          if (ev.key === "Backspace" && !champ.value && cases[index - 1]) {
            cases[index - 1].focus();
          }
        });

        /* Un code reçu par SMS est collé d'un bloc : on le
           répartit sur l'ensemble des cases. */
        champ.addEventListener("paste", function (ev) {
          ev.preventDefault();
          var colle = (ev.clipboardData || window.clipboardData).getData("text").replace(/\D/g, "");
          cases.forEach(function (c, i) {
            c.value = colle[i] || "";
            signalerSaisie(c);
          });
          var cible = cases[Math.min(colle.length, cases.length - 1)];
          if (cible) cible.focus();
        });
      });
    });
  }

  /* -----------------------------------------------------------
     Saisie de date en trois champs

     Le passage d'un champ au suivant est automatique, mais jamais
     bloquant : on n'interdit pas la frappe, on n'impose pas de
     format, et le retour arrière ramène au champ précédent.
     ----------------------------------------------------------- */
  function initDates(racine) {
    $$(".fs-date", racine).forEach(function (bloc) {
      if (!nouveau(bloc, "dates")) return;
      var champs = $$('input', bloc);

      champs.forEach(function (champ, index) {
        var taille = parseInt(champ.getAttribute("maxlength"), 10) || 2;

        champ.addEventListener("input", function () {
          if (ECRITURE_SCRIPT) return;
          champ.value = champ.value.replace(/\D/g, "").slice(0, taille);
          if (champ.value.length === taille && champs[index + 1]) {
            champs[index + 1].focus();
            champs[index + 1].select();
          }
        });

        champ.addEventListener("keydown", function (ev) {
          if (ev.key === "Backspace" && !champ.value && champs[index - 1]) {
            champs[index - 1].focus();
          }
        });

        /* Une date collée depuis le presse-papiers est répartie
           sur les trois champs plutôt que refusée. */
        champ.addEventListener("paste", function (ev) {
          var colle = (ev.clipboardData || window.clipboardData).getData("text");
          var parts = colle.split(/[^\d]+/).filter(Boolean);
          if (parts.length < 3) return;
          ev.preventDefault();
          champs.forEach(function (c, i) {
            if (!parts[i]) return;
            c.value = parts[i];
            signalerSaisie(c);
          });
        });
      });
    });
  }

  /* -----------------------------------------------------------
     Autocomplétion

     Motif ARIA combobox : le champ conserve le focus, la
     suggestion courante est désignée par aria-activedescendant.
     Sans JavaScript, le champ reste une saisie libre que le
     serveur valide — la liste n'est qu'une aide.
     ----------------------------------------------------------- */
  function initCombos(racine) {
    $$("[data-combo]", racine).forEach(function (bloc) {
      if (!nouveau(bloc, "combos")) return;
      var champ = $('input', bloc);
      var liste = $('.fs-combo-liste', bloc);
      if (!champ || !liste) return;

      var compteur = resoudre(champ, champ.getAttribute("aria-describedby"));
      var options = $$('.fs-combo-option', liste).map(function (li) {
        return { element: li, texte: li.textContent.trim() };
      });
      var visibles = [];
      var actif = -1;

      champ.setAttribute("role", "combobox");
      champ.setAttribute("aria-expanded", "false");
      champ.setAttribute("aria-autocomplete", "list");
      champ.setAttribute("autocomplete", "off");
      liste.setAttribute("role", "listbox");
      options.forEach(function (o, i) {
        o.element.setAttribute("role", "option");
        o.element.id = o.element.id || liste.id + "-opt-" + i;
      });

      function sansAccent(s) {
        return s.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase();
      }

      function surligner(option, saisie) {
        var texte = option.texte;
        if (!saisie) { option.element.textContent = texte; return; }
        var i = sansAccent(texte).indexOf(sansAccent(saisie));
        if (i === -1) { option.element.textContent = texte; return; }
        option.element.textContent = "";
        option.element.append(
          texte.slice(0, i),
          Object.assign(document.createElement("mark"), { textContent: texte.slice(i, i + saisie.length) }),
          texte.slice(i + saisie.length)
        );
      }

      function fermer() {
        liste.hidden = true;
        champ.setAttribute("aria-expanded", "false");
        champ.removeAttribute("aria-activedescendant");
        actif = -1;
      }

      function designer(index) {
        visibles.forEach(function (o) { o.element.setAttribute("aria-selected", "false"); });
        actif = index;
        if (index < 0) { champ.removeAttribute("aria-activedescendant"); return; }
        var o = visibles[index];
        o.element.setAttribute("aria-selected", "true");
        champ.setAttribute("aria-activedescendant", o.element.id);
        o.element.scrollIntoView({ block: "nearest" });
      }

      function filtrer() {
        var saisie = champ.value.trim();
        var cle = sansAccent(saisie);

        visibles = options.filter(function (o) {
          var correspond = !cle || sansAccent(o.texte).indexOf(cle) !== -1;
          o.element.hidden = !correspond;
          if (correspond) surligner(o, saisie);
          return correspond;
        });

        var vide = $('.fs-combo-vide', liste);
        if (vide) vide.hidden = visibles.length > 0;

        liste.hidden = false;
        champ.setAttribute("aria-expanded", "true");
        designer(-1);

        /* Le nombre de résultats est annoncé poliment : sans cela,
           un usager au lecteur d'écran ne sait pas que la liste a
           changé sous ses doigts. */
        if (compteur) {
          compteur.textContent = visibles.length
            ? visibles.length + " résultat" + (visibles.length > 1 ? "s" : "") + " disponible" + (visibles.length > 1 ? "s" : "")
            : "Aucun résultat";
        }
      }

      function choisir(option) {
        champ.value = option.texte;
        fermer();
        signalerSaisie(champ);
      }

      champ.addEventListener("input", function () {
        if (ECRITURE_SCRIPT) return;
        filtrer();
      });
      champ.addEventListener("focus", filtrer);

      champ.addEventListener("keydown", function (ev) {
        if (ev.key === "ArrowDown" || ev.key === "ArrowUp") {
          ev.preventDefault();
          if (liste.hidden) { filtrer(); return; }

          var n = visibles.length;
          if (!n) return;

          /* La liste boucle : depuis le dernier élément, la flèche
             bas revient au premier. Depuis l'état « aucune
             suggestion désignée », la flèche haut va au dernier. */
          designer(ev.key === "ArrowDown"
            ? (actif + 1) % n
            : (actif <= 0 ? n - 1 : actif - 1));
        } else if (ev.key === "Enter") {
          if (!liste.hidden && actif >= 0) {
            ev.preventDefault();
            choisir(visibles[actif]);
          }
        } else if (ev.key === "Escape") {
          fermer();
        }
      });

      liste.addEventListener("click", function (ev) {
        var li = ev.target.closest(".fs-combo-option");
        if (!li) return;
        var o = options.filter(function (x) { return x.element === li; })[0];
        if (o) choisir(o);
      });

      document.addEventListener("click", function (ev) {
        if (!bloc.contains(ev.target)) fermer();
      });
    });
  }

  /* -----------------------------------------------------------
     Graphiques en barres

     La largeur est calculée à partir de data-valeur et du
     maximum déclaré sur le graphique, pour que le balisage porte
     la donnée réelle et non une largeur déjà calculée.
     ----------------------------------------------------------- */
  function appliquerBarres(groupe) {
    var max = parseFloat(groupe.getAttribute("data-max"));
    if (!max) return;

    $$(".fs-barre[data-valeur]", groupe).forEach(function (barre) {
      var valeur = parseFloat(barre.getAttribute("data-valeur")) || 0;
      if (repris(barre, "barre", { valeur: valeur, max: max })) return;

      var remplissage = $(".fs-barre-remplissage", barre);
      if (remplissage) {
        remplissage.style.width = Math.max(0, Math.min(100, (valeur / max) * 100)) + "%";
      }
    });
  }

  function initBarres(racine) {
    $$(".fs-barres[data-max]", racine).forEach(function (groupe) {
      appliquerBarres(groupe);

      /* Le suivi porte sur le sous-arbre : il résiste au remplacement
         des barres elles-mêmes par une boucle du cadriciel. */
      if (!nouveau(groupe, "barres")) return;
      suivreValeurs(groupe, ["data-valeur", "data-max"], function () {
        appliquerBarres(groupe);
      });
    });
  }

  /* -----------------------------------------------------------
     Tri de tableau

     Tri côté client, pour les volumes réduits. Au-delà de
     quelques centaines de lignes, le tri doit être fait par le
     serveur avec pagination.
     ----------------------------------------------------------- */
  function initTris(racine) {
    $$("table[data-triable]", racine).forEach(function (table) {
      if (!nouveau(table, "tris")) return;
      var corps = $("tbody", table);
      if (!corps) return;

      $$("th[aria-sort]", table).forEach(function (entete, colonne) {
        var btn = $("button", entete);
        if (!btn) return;

        btn.addEventListener("click", function () {
          var sensActuel = entete.getAttribute("aria-sort");
          var croissant = sensActuel !== "ascending";

          $$("th[aria-sort]", table).forEach(function (autre) {
            autre.setAttribute("aria-sort", "none");
          });
          entete.setAttribute("aria-sort", croissant ? "ascending" : "descending");

          var index = Array.prototype.indexOf.call(entete.parentNode.children, entete);
          var lignes = $$("tr", corps);

          lignes.sort(function (a, b) {
            var va = valeurCellule(a.children[index]);
            var vb = valeurCellule(b.children[index]);
            var na = parseFloat(String(va).replace(/[^\d.,-]/g, "").replace(",", "."));
            var nb = parseFloat(String(vb).replace(/[^\d.,-]/g, "").replace(",", "."));

            var comparaison;
            if (!isNaN(na) && !isNaN(nb)) {
              comparaison = na - nb;
            } else {
              comparaison = String(va).localeCompare(String(vb), "fr", { sensitivity: "base" });
            }
            return croissant ? comparaison : -comparaison;
          });

          lignes.forEach(function (ligne) { corps.appendChild(ligne); });

          /* Le tri réordonne l'ensemble des lignes, y compris
             celles que la pagination masque. Elle doit donc
             reprendre la main pour réafficher la bonne tranche. */
          diffuser(table, "donnees", null);
        });
      });

      /* data-valeur permet de trier sur une donnée brute (date
         ISO, montant non formaté) différente de l'affichage. */
      function valeurCellule(cellule) {
        if (!cellule) return "";
        return cellule.getAttribute("data-valeur") || cellule.textContent.trim();
      }
    });
  }

  /* -----------------------------------------------------------
     Pagination de tableau

     Pagination dans le navigateur, réservée aux volumes réduits :
     toutes les lignes sont déjà dans la page. Au-delà de quelques
     centaines, la pagination revient au serveur et chaque page
     porte sa propre adresse.

     Sans JavaScript, aucune commande n'est construite et le
     tableau s'affiche en entier : c'est une dégradation correcte,
     l'usager voit toutes les données.
     ----------------------------------------------------------- */

  /* -----------------------------------------------------------
     Surveillance du jeu de lignes

     Un cadriciel qui re-rend son tableau remplace les lignes :
     celles que la pagination masquait réapparaissent, celles
     qu'un filtre écartait reviennent. Plutôt que d'interdire le
     cas, on le détecte et l'on rejoue.

     Seules les additions et suppressions comptent. Un tri
     réordonne les mêmes nœuds : le jeu est inchangé, rien n'est
     rejoué, et aucune boucle ne peut s'amorcer.
     ----------------------------------------------------------- */
  function surLignesChangees(corps, rappel) {
    if (!NAVIGATEUR || !window.MutationObserver) return;

    var connues = new Set($$("tr", corps));

    new MutationObserver(function () {
      var actuelles = $$("tr", corps);
      var identique = actuelles.length === connues.size &&
                      actuelles.every(function (tr) { return connues.has(tr); });
      if (identique) return;

      connues = new Set(actuelles);
      rappel();
    }).observe(corps, { childList: true });
  }

  /* Fenêtre de pages autour de la page courante, avec les
     extrémités toujours visibles : 1 … 4 5 6 … 18 */
  function fenetrePages(courante, total) {
    if (total <= 7) {
      return Array.from({ length: total }, function (_, i) { return i + 1; });
    }

    var pages = [1];
    var debut = Math.max(2, courante - 1);
    var fin = Math.min(total - 1, courante + 1);

    if (courante <= 3) fin = 4;
    if (courante >= total - 2) debut = total - 3;

    if (debut > 2) pages.push("…");
    for (var p = debut; p <= fin; p++) pages.push(p);
    if (fin < total - 1) pages.push("…");
    pages.push(total);

    return pages;
  }

  function initPaginations(racine) {
    $$("[data-pagination]", racine).forEach(function (bloc) {
      if (!nouveau(bloc, "paginations")) return;
      var table = $("table", bloc);
      var corps = table && $("tbody", table);
      var nav = $(".fs-pagination", bloc);
      if (!corps || !nav) return;

      var intervalle = $(".fs-tableau-intervalle", bloc);
      var total = $(".fs-tableau-compte b", bloc);
      var selecteur = $(".fs-tableau-taille select", bloc);

      var taille = parseInt(bloc.getAttribute("data-pagination"), 10) || 10;
      var page = 1;
      var dernierCompte = -1;

      var annonce = document.createElement("p");
      annonce.className = "fs-invisible";
      annonce.setAttribute("role", "status");
      annonce.setAttribute("aria-live", "polite");
      bloc.appendChild(annonce);

      /* Seules les lignes retenues par les filtres entrent dans la
         pagination : une ligne exclue ne compte pas et n'occupe
         aucune place sur une page. */
      function lignes() { return $$("tr:not([data-exclu])", corps); }

      function afficher(nouvelle, annoncer) {
        var toutes = lignes();
        var pages = Math.max(1, Math.ceil(toutes.length / taille));
        page = Math.min(Math.max(1, nouvelle), pages);

        var debut = (page - 1) * taille;
        var fin = Math.min(debut + taille, toutes.length);

        toutes.forEach(function (tr, i) {
          tr.hidden = i < debut || i >= fin;
        });

        if (total) total.textContent = toutes.length.toLocaleString("fr-FR");
        if (intervalle) {
          intervalle.textContent = toutes.length
            ? "Résultats " + (debut + 1).toLocaleString("fr-FR") + " à " +
              fin.toLocaleString("fr-FR") + " sur " + toutes.length.toLocaleString("fr-FR")
            : "Aucun résultat";
        }

        construire(pages);
        dernierCompte = toutes.length;

        if (annoncer) {
          annonce.textContent = "Page " + page + " sur " + pages + ". " +
            (intervalle ? intervalle.textContent + "." : "");
        }
      }

      function bouton(contenu, cible, options) {
        options = options || {};
        var b = document.createElement("button");
        b.type = "button";
        b.textContent = contenu;
        if (options.libelle) b.setAttribute("aria-label", options.libelle);
        if (options.courante) b.setAttribute("aria-current", "page");
        if (options.inactif) b.disabled = true;
        else b.addEventListener("click", function () {
          afficher(cible, true);
          /* Le focus reste sur la commande utilisée : le renvoyer
             en haut du tableau ferait perdre sa place à l'usager
             qui parcourt la pagination au clavier. */
        });
        return b;
      }

      function construire(pages) {
        nav.textContent = "";

        /* Une pagination d'une seule page n'apporte rien et
           ajoute deux commandes inertes. */
        nav.hidden = pages <= 1;
        if (nav.hidden) return;

        nav.appendChild(bouton("‹", page - 1, {
          libelle: "Page précédente", inactif: page === 1
        }));

        fenetrePages(page, pages).forEach(function (p) {
          if (p === "…") {
            var s = document.createElement("span");
            s.className = "fs-ellipse";
            s.setAttribute("aria-hidden", "true");
            s.textContent = "…";
            nav.appendChild(s);
            return;
          }
          nav.appendChild(bouton(String(p), p, {
            libelle: "Page " + p, courante: p === page
          }));
        });

        nav.appendChild(bouton("›", page + 1, {
          libelle: "Page suivante", inactif: page === pages
        }));
      }

      if (selecteur) {
        selecteur.addEventListener("change", function () {
          taille = parseInt(selecteur.value, 10) || taille;
          afficher(1, true);
        });
      }

      /* Un tri ou un changement de filtre renvoie à la première
         page : rester en page 7 d'un jeu qui n'en compte plus que
         deux n'aurait aucun sens. */
      /* Un re-rendu qui remplace les lignes sans changer leur nombre
         n'est pas un autre jeu de données : c'est la même page,
         reconstruite, et l'usager doit y rester. Un tri, un filtre ou
         un nombre de lignes différent renvoient en tête. */
      function apres(rejeu, annoncer) {
        afficher(rejeu && lignes().length === dernierCompte ? page : 1, annoncer);
      }

      bloc.addEventListener("fs:donnees", function (ev) {
        var detail = ev.detail || {};
        apres(!!detail.rejeu, detail.annoncer);
      });

      afficher(1, false);

      /* Lorsque le bloc porte aussi des filtres, ceux-ci recalculent
         les lignes retenues avant d'émettre fs:donnees : les observer
         tous les deux ferait passer la pagination deux fois, la
         première sur un état intermédiaire. Un seul pilote. */
      if (!bloc.hasAttribute("data-filtres")) {
        surLignesChangees(corps, function () { apres(true, false); });
      }
    });
  }

  /* -----------------------------------------------------------
     Filtres de tableau

     Les contrôles déclarent ce qu'ils filtrent par data-filtre, et
     sur quelle colonne par data-filtre-colonne. La colonne est
     désignée par son intitulé, celui que les cellules portent déjà
     dans data-colonne pour le mode empilé : le même attribut sert
     donc à la lisibilité sur mobile et au filtrage.
     ----------------------------------------------------------- */
  function initFiltres(racine) {
    $$("[data-filtres]", racine).forEach(function (bloc) {
      if (!nouveau(bloc, "filtres")) return;
      var table = $("table", bloc);
      var corps = table && $("tbody", table);
      if (!corps) return;

      var controles = $$("[data-filtre]", bloc);
      var zone = $(".fs-filtres-actifs", bloc);
      var vide = $(".fs-tableau-vide", bloc);
      var cadre = $(".fs-tableau-cadre", bloc);
      var pied = $(".fs-tableau-pied", bloc);
      if (!controles.length) return;

      function sansAccent(s) {
        return s.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase();
      }

      function valeur(ligne, colonne) {
        var cellule = ligne.querySelector('[data-colonne="' + colonne + '"]');
        if (!cellule) return "";
        return (cellule.getAttribute("data-valeur") || cellule.textContent).trim();
      }

      var ISO = /^\d{4}-\d{2}-\d{2}/;

      /* Les dates ISO sont comparées comme du texte, ce qui donne
         le bon ordre. Les traiter en nombres les tronquerait à
         l'année : parseFloat("2026-09-18") vaut 2026, et toutes
         les dates d'une même année deviendraient égales. */
      function compare(a, b) {
        a = String(a); b = String(b);
        if (ISO.test(a) && ISO.test(b)) return a.localeCompare(b);

        var na = parseFloat(a.replace(/[^\d.,-]/g, "").replace(",", "."));
        var nb = parseFloat(b.replace(/[^\d.,-]/g, "").replace(",", "."));
        if (!isNaN(na) && !isNaN(nb)) return na - nb;

        return a.localeCompare(b, "fr");
      }

      function libelle(controle) {
        if (controle.getAttribute("data-filtre-libelle")) {
          return controle.getAttribute("data-filtre-libelle");
        }
        var etiquette = controle.id && $('label[for="' + controle.id + '"]', bloc);
        return etiquette ? etiquette.textContent.trim() : "Filtre";
      }

      function retient(ligne, controle) {
        var saisie = controle.value.trim();
        if (!saisie) return true;

        var type = controle.getAttribute("data-filtre");
        var colonne = controle.getAttribute("data-filtre-colonne");

        if (type === "recherche") {
          return sansAccent(ligne.textContent).indexOf(sansAccent(saisie)) !== -1;
        }
        if (type === "egal") {
          return sansAccent(valeur(ligne, colonne)) === sansAccent(saisie);
        }
        if (type === "min") return compare(valeur(ligne, colonne), saisie) >= 0;
        if (type === "max") return compare(valeur(ligne, colonne), saisie) <= 0;
        return true;
      }

      function rappel(actifs) {
        if (!zone) return;
        zone.textContent = "";
        zone.hidden = !actifs.length;
        if (!actifs.length) return;

        var titre = document.createElement("span");
        titre.className = "fs-filtres-actifs-titre";
        titre.textContent = "Filtres actifs";
        zone.appendChild(titre);

        actifs.forEach(function (controle) {
          var puce = document.createElement("span");
          puce.className = "fs-etiquette";
          puce.append(libelle(controle) + " : " + controle.value.trim());

          var retirer = document.createElement("button");
          retirer.type = "button";
          retirer.setAttribute("aria-label", "Retirer le filtre " + libelle(controle));
          retirer.textContent = "×";
          retirer.addEventListener("click", function () {
            controle.value = "";
            signalerSaisie(controle);
            appliquer(true);
            controle.focus();
          });

          puce.appendChild(retirer);
          zone.appendChild(puce);
        });

        var vider = document.createElement("button");
        vider.type = "button";
        vider.className = "fs-filtres-vider";
        vider.textContent = "Tout effacer";
        vider.addEventListener("click", function () {
          controles.forEach(function (c) {
            c.value = "";
            signalerSaisie(c);
          });
          appliquer(true);
          controles[0].focus();
        });
        zone.appendChild(vider);
      }

      function appliquer(annoncer, rejeu) {
        var actifs = controles.filter(function (c) { return c.value.trim(); });

        var retenues = 0;
        $$("tr", corps).forEach(function (ligne) {
          var garde = controles.every(function (c) { return retient(ligne, c); });
          if (garde) { ligne.removeAttribute("data-exclu"); retenues++; }
          else ligne.setAttribute("data-exclu", "");
        });

        rappel(actifs);

        /* L'état vide distingue « aucune donnée » de « aucun
           résultat pour ces filtres » : ce n'est pas la même
           information, et la seconde appelle une action. */
        if (vide) {
          vide.hidden = retenues > 0;
          var titre = $(".fs-tableau-vide-titre", vide);
          var texte = $("[data-vide-texte]", vide);
          if (titre) {
            titre.textContent = actifs.length
              ? "Aucun résultat pour ces filtres"
              : "Aucune donnée à afficher";
          }
          if (texte) {
            texte.textContent = actifs.length
              ? "Élargissez ou retirez un filtre pour retrouver des résultats."
              : "Les données apparaîtront ici dès qu'elles seront disponibles.";
          }
        }
        if (cadre) cadre.hidden = retenues === 0;
        if (pied) pied.hidden = retenues === 0;

        diffuser(bloc, "donnees", {
          annoncer: !!annoncer, rejeu: !!rejeu, retenues: retenues
        });
      }

      controles.forEach(function (controle) {
        var evenement = controle.tagName === "SELECT" ? "change" : "input";
        controle.addEventListener(evenement, function () {
          if (ECRITURE_SCRIPT) return;
          appliquer(true);
        });
      });

      appliquer(false);

      /* Le rejeu est signalé comme tel : la pagination sait alors
         qu'il s'agit du même jeu reconstruit, et non d'un filtre que
         l'usager vient de changer. */
      surLignesChangees(corps, function () { appliquer(false, true); });
    });
  }

  /* -----------------------------------------------------------
     Extraction d'un tableau

     CSV produit dans le navigateur, PDF obtenu par l'impression.

     Aucune bibliothèque n'est employée pour fabriquer un PDF :
     les plus légères pèsent plusieurs centaines de kilo-octets,
     soit davantage que l'ensemble du système, pour un résultat
     inférieur à ce que le navigateur sait déjà faire. L'impression
     produit un document paginé, avec l'emblème, l'en-tête de
     colonnes répété et le contexte d'extraction ; tout système
     d'exploitation sait l'enregistrer en PDF. Lorsqu'un PDF doit
     être joint à un envoi — récépissé, décision —, il est produit
     par le serveur, qui seul peut le signer.
     ----------------------------------------------------------- */

  var MOIS = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet",
              "août", "septembre", "octobre", "novembre", "décembre"];

  function dateDuJour() {
    var d = new Date();
    return d.getDate() + " " + MOIS[d.getMonth()] + " " + d.getFullYear();
  }

  function horodatage() {
    var d = new Date();
    var p = function (n) { return String(n).padStart(2, "0"); };
    return d.getFullYear() + "-" + p(d.getMonth() + 1) + "-" + p(d.getDate());
  }

  /* Le point-virgule est le séparateur attendu par les tableurs
     configurés en français ; la virgule y est le séparateur
     décimal. Un CSV à la virgule s'ouvre en une seule colonne. */
  function echapperCsv(valeur) {
    valeur = String(valeur).replace(/\s+/g, " ").trim();
    if (/[";\n\r]/.test(valeur)) return '"' + valeur.replace(/"/g, '""') + '"';
    return valeur;
  }

  function initExports(racine) {
    $$("[data-export]", racine).forEach(function (bloc) {
      if (!nouveau(bloc, "exports")) return;
      var table = $("table", bloc);
      var corps = table && $("tbody", table);
      if (!corps) return;

      var nom = bloc.getAttribute("data-export") || "extraction";
      var contexte = $("[data-impression-contexte]", bloc);

      /* Une colonne ne portant que des commandes n'a rien à faire
         dans une extraction : elle est marquée data-export="non". */
      var entetes = $$("thead th", table).filter(function (th) {
        return th.getAttribute("data-export") !== "non";
      });
      var index = entetes.map(function (th) {
        return Array.prototype.indexOf.call(th.parentNode.children, th);
      });

      function lignesRetenues() { return $$("tr:not([data-exclu])", corps); }

      function filtresActifs() {
        return $$("[data-filtre]", bloc)
          .filter(function (c) { return c.value.trim(); })
          .map(function (c) {
            var e = c.id && $('label[for="' + c.id + '"]', bloc);
            return (e ? e.textContent.trim() : "Filtre") + " : " + c.value.trim();
          });
      }

      function majContexte() {
        if (!contexte) return;
        var filtres = filtresActifs();
        var n = lignesRetenues().length;
        contexte.textContent =
          n.toLocaleString("fr-FR") + (n > 1 ? " lignes" : " ligne") +
          " — extrait le " + dateDuJour() +
          (filtres.length ? " — filtres appliqués : " + filtres.join(" ; ")
                          : " — aucun filtre appliqué");
      }

      function versCsv() {
        var lignes = [entetes.map(function (th) {
          return echapperCsv(th.textContent);
        }).join(";")];

        lignesRetenues().forEach(function (tr) {
          var cellules = tr.children;
          lignes.push(index.map(function (i) {
            var td = cellules[i];
            if (!td) return "";
            /* La donnée brute prime sur l'affichage : une date
               part en ISO et un montant en nombre, directement
               exploitables dans un tableur. */
            return echapperCsv(td.getAttribute("data-valeur") || td.textContent);
          }).join(";"));
        });

        return lignes.join("\r\n");
      }

      /* Garde-fou contre l'extraction partielle.

         L'export lit les lignes présentes dans le document. Si un
         cadriciel n'en rend qu'une partie — sa propre pagination,
         un défilement virtuel — le fichier serait incomplet sans
         que rien ne le signale, et l'usager croirait détenir
         l'intégralité de ses dossiers.

         Le service déclare alors data-export-total. Dès que le
         compte ne correspond pas, l'extraction est refusée : mieux
         vaut un refus explicite qu'un fichier faux. */
      function verifierCompletude() {
        var declare = parseInt(bloc.getAttribute("data-export-total"), 10);
        if (isNaN(declare)) return true;

        var presentes = $$("tr", corps).length;
        if (presentes >= declare) return true;

        notifier({
          titre: "Extraction refusée",
          texte: presentes + " lignes sur " + declare + " sont chargées. "
               + "L'export doit être produit par le service.",
          ton: "danger",
          duree: 0
        });
        return false;
      }

      function telecharger() {
        if (!verifierCompletude()) return;

        var filtres = filtresActifs().length;
        var fichier = nom + "-" + horodatage() + (filtres ? "-filtre" : "") + ".csv";

        /* L'indicateur d'ordre des octets est indispensable :
           sans lui, un tableur lit le fichier en encodage local et
           les accents deviennent illisibles. */
        var blob = new Blob(["﻿" + versCsv()], { type: "text/csv;charset=utf-8;" });
        var url = URL.createObjectURL(blob);

        var lien = document.createElement("a");
        lien.href = url;
        lien.download = fichier;
        document.body.appendChild(lien);
        lien.click();
        lien.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);

        notifier({
          titre: "Extraction téléchargée",
          texte: fichier + " — " + lignesRetenues().length + " ligne(s).",
          ton: "succes"
        });
      }

      $$("[data-export-csv]", bloc).forEach(function (b) {
        b.addEventListener("click", telecharger);
      });

      $$("[data-export-impression]", bloc).forEach(function (b) {
        b.addEventListener("click", function () {
          if (!verifierCompletude()) return;
          majContexte();
          window.print();
        });
      });

      bloc.addEventListener("fs:donnees", majContexte);
      majContexte();
    });
  }

  /* -----------------------------------------------------------
     Encarts refermables

     Le message disparaît, mais le focus ne doit pas disparaître
     avec lui : sans reprise explicite, l'usager au clavier est
     renvoyé en début de document.
     ----------------------------------------------------------- */
  function initEncarts() {
    document.addEventListener("click", function (ev) {
      var bouton = cibleDe(ev).closest("[data-ferme-encart]");
      if (!bouton) return;

      var encart = bouton.closest(".fs-encart");
      if (!encart) return;

      /* Lorsqu'un cadriciel possède cet encart, le retirer d'ici
         laisserait sa vue croire qu'il existe encore. L'annonce est
         donc annulable : le projet l'intercepte, empêche le retrait,
         et masque l'encart par sa propre liaison. */
      if (repris(encart, "fermeture", null)) return;

      var suivant = encart.nextElementSibling;
      var repli = encart.parentElement;

      encart.remove();

      var cible = (suivant && $(FOCUSABLES, suivant)) ||
                  (repli && $(FOCUSABLES, repli));
      if (cible) cible.focus();

      notifier({ texte: "Message fermé.", duree: 2000 });
    });
  }

  /* -----------------------------------------------------------
     Centre de notifications
     ----------------------------------------------------------- */
  function initCentreNotifications(racine) {
    $$(".fs-notifications", racine).forEach(function (centre) {
      if (!nouveau(centre, "notifications")) return;
      var compteur = $("[data-notifications-compte]", centre);

      function majCompteur() {
        var n = $$('.fs-notification[data-lu="false"]', centre).length;
        if (compteur) {
          /* L'écriture n'a lieu que si le compte a changé : le compteur
             est lui-même sous observation, et réécrire la même valeur
             relancerait le balayage sans fin. */
          if (compteur.textContent !== String(n)) compteur.textContent = String(n);
          compteur.hidden = n === 0;
        }
        var tout = $("[data-tout-lu]", centre);
        if (tout) tout.disabled = n === 0;
      }

      centre.addEventListener("click", function (ev) {
        var cible = cibleDe(ev);

        var lire = cible.closest("[data-marquer-lu]");
        if (lire) {
          var item = lire.closest(".fs-notification");
          if (item && !repris(item, "lecture", { tout: false })) {
            item.setAttribute("data-lu", "true");
          }
          majCompteur();
          return;
        }

        if (cible.closest("[data-tout-lu]")) {
          $$(".fs-notification", centre).forEach(function (n) {
            if (!repris(n, "lecture", { tout: true })) {
              n.setAttribute("data-lu", "true");
            }
          });
          majCompteur();
        }
      });

      majCompteur();

      /* Quand la liste vient d'une boucle du cadriciel, l'état lu est
         à lui ; le compteur, lui, reste juste. */
      suivreValeurs(centre, ["data-lu"], majCompteur);
    });
  }

  /* -----------------------------------------------------------
     Calendrier

     Le mois est produit par le serveur ; le script ne gère que la
     sélection, pour que le calendrier reste consultable sans lui.
     ----------------------------------------------------------- */
  function initCalendriers(racine) {
    $$(".fs-calendrier", racine).forEach(function (calendrier) {
      if (!nouveau(calendrier, "calendriers")) return;
      var sortie = $("[data-calendrier-valeur]", calendrier);

      calendrier.addEventListener("click", function (ev) {
        var jour = cibleDe(ev).closest(".fs-jour");
        if (!jour || jour.disabled || jour.getAttribute("aria-disabled") === "true") return;

        $$(".fs-jour", calendrier).forEach(function (j) {
          if (j.hasAttribute("aria-pressed")) j.setAttribute("aria-pressed", "false");
        });
        jour.setAttribute("aria-pressed", "true");

        if (sortie) {
          sortie.textContent = jour.getAttribute("data-date-libelle") || jour.textContent.trim();
        }
      });
    });
  }

  /* -----------------------------------------------------------
     Compteur de caractères
     ----------------------------------------------------------- */
  function initCompteurs(racine) {
    $$("[data-compteur]", racine).forEach(function (champ) {
      if (!nouveau(champ, "compteurs")) return;
      var sortie = resoudre(champ, champ.getAttribute("data-compteur"));
      if (!sortie) return;

      var max = parseInt(champ.getAttribute("maxlength"), 10);

      function majAffichage() {
        var restant = max - champ.value.length;
        sortie.textContent = restant >= 0
          ? restant + " caractère" + (restant > 1 ? "s" : "") + " restant" + (restant > 1 ? "s" : "")
          : "Limite dépassée";
        sortie.classList.toggle("fs-message--erreur", restant < 0);
      }

      champ.addEventListener("input", majAffichage);
      majAffichage();
    });
  }

  /* -----------------------------------------------------------
     Barre de progression pilotée par data-valeur
     ----------------------------------------------------------- */
  function appliquerJauge(jauge) {
    var valeur = Math.max(0, Math.min(100, parseFloat(jauge.getAttribute("data-valeur")) || 0));

    jauge.setAttribute("role", "progressbar");
    jauge.setAttribute("aria-valuemin", "0");
    jauge.setAttribute("aria-valuemax", "100");

    /* Le projet qui lie lui-même [style.width] annule l'annonce : la
       largeur reste à sa liaison, et l'état accessible avec elle. */
    if (repris(jauge, "jauge", { valeur: valeur })) return;

    jauge.setAttribute("aria-valuenow", String(valeur));
    var barre = $(".fs-jauge-valeur", jauge);
    if (barre) barre.style.width = valeur + "%";
  }

  function initJauges(racine) {
    $$(".fs-jauge[data-valeur]", racine).forEach(function (jauge) {
      appliquerJauge(jauge);

      /* La valeur est suivie : une jauge dont le cadriciel change
         data-valeur se met à jour sans nouvelle initialisation. */
      if (!nouveau(jauge, "jauges")) return;
      suivreValeurs(jauge, ["data-valeur"], function () { appliquerJauge(jauge); });
    });
  }

  /* -----------------------------------------------------------
     Amorçage
     ----------------------------------------------------------- */
  /* -----------------------------------------------------------
     Amorçage

     Deux étages, et la distinction n'est pas cosmétique.

     Les comportements délégués posent un gestionnaire unique sur
     le document : ils doivent être enregistrés une seule fois pour
     toute la vie de la page. Les rappeler empilerait les
     gestionnaires, et un clic sur « fermer » fermerait deux fois.

     Les comportements liés élément par élément, eux, doivent
     pouvoir être rejoués sur un sous-arbre fraîchement rendu. Ils
     sont idempotents : un élément déjà initialisé est ignoré.
     ----------------------------------------------------------- */

  var GLOBAL_FAIT = false;

  function initGlobal() {
    if (GLOBAL_FAIT || !NAVIGATEUR) return;
    GLOBAL_FAIT = true;
    initTheme();
    initNavigation();
    initModales();
    initPanneaux();
    initMenus();
    initEncarts();
  }

  /* initialiser() sans argument parcourt le document entier ;
     avec un élément, il se limite à son sous-arbre. C'est la
     seule fonction qu'un cadriciel a besoin d'appeler. */
  function initialiser(racine) {
    if (!NAVIGATEUR) return;
    initGlobal();

    var zone = racine || document;
    initOnglets(zone);
    initModalesLocales(zone);
    initDepots(zone);
    initCodes(zone);
    initDates(zone);
    initCombos(zone);
    initTris(zone);
    initPaginations(zone);
    initFiltres(zone);
    initExports(zone);
    initCentreNotifications(zone);
    initCalendriers(zone);
    initCompteurs(zone);
    initJauges(zone);
    initBarres(zone);
  }

  /* -----------------------------------------------------------
     Observation du DOM

     Facultatif, et destiné aux applications qui rendent leur
     balisage après le chargement. Une fois activé, plus rien
     n'est à appeler : tout sous-arbre ajouté est initialisé.

     Le balayage est groupé par image d'affichage. Sans cela, un
     rendu qui insère cent nœuds déclencherait cent balayages.
     ----------------------------------------------------------- */
  var OBSERVATEUR = null;

  function observer(actif) {
    if (!NAVIGATEUR) return false;

    if (actif === false) {
      if (OBSERVATEUR) { OBSERVATEUR.disconnect(); OBSERVATEUR = null; }
      return false;
    }
    if (OBSERVATEUR) return true;

    var prevu = false;
    OBSERVATEUR = new MutationObserver(function () {
      if (prevu) return;
      prevu = true;
      requestAnimationFrame(function () {
        prevu = false;
        initialiser();
      });
    });
    OBSERVATEUR.observe(document.documentElement, { childList: true, subtree: true });
    return true;
  }

  /* Construction d'un CSV à partir de données, et non du document.
     C'est la porte de sortie lorsque le cadriciel possède les
     lignes : il fournit l'intégralité du jeu, la charte se charge
     de l'échappement, du séparateur et de l'encodage. */
  function versCsv(entetes, lignes) {
    var sortie = [entetes.map(echapperCsv).join(";")];
    lignes.forEach(function (ligne) {
      sortie.push(ligne.map(echapperCsv).join(";"));
    });
    return sortie.join("\r\n");
  }

  function telechargerCsv(nom, contenu) {
    if (!NAVIGATEUR) return;

    var blob = new Blob(["﻿" + contenu], { type: "text/csv;charset=utf-8;" });
    var url = URL.createObjectURL(blob);
    var lien = document.createElement("a");
    lien.href = url;
    lien.download = /\.csv$/.test(nom) ? nom : nom + ".csv";
    document.body.appendChild(lien);
    lien.click();
    lien.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
  }

  var Faso = {
    notifier: notifier,
    appliquerTheme: appliquerTheme,
    initialiser: initialiser,
    observer: observer,
    versCsv: versCsv,
    telechargerCsv: telechargerCsv,
    version: "2.0.0",

    /* Les comportements sont exposés pour que la documentation
       puisse afficher leur source réelle, lue par toString().
       Le code montré est donc celui qui s'exécute, et non une
       copie entretenue à la main. */
    comportements: {
      theme: initTheme,
      navigation: initNavigation,
      onglets: initOnglets,
      modales: initModales,
      modalesLocales: initModalesLocales,
      panneaux: initPanneaux,
      menus: initMenus,
      depots: initDepots,
      codes: initCodes,
      dates: initDates,
      combos: initCombos,
      tris: initTris,
      paginations: initPaginations,
      filtres: initFiltres,
      exports: initExports,
      encarts: initEncarts,
      centreNotifications: initCentreNotifications,
      calendriers: initCalendriers,
      compteurs: initCompteurs,
      jauges: initJauges,
      barres: initBarres,
      notifier: notifier
    }
  };

  /* Rien ne s'exécute hors navigateur : le module peut être
     importé par un rendu côté serveur sans le faire échouer. */
  if (NAVIGATEUR) {
    window.Faso = Faso;

    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", function () { initialiser(); });
    } else {
      initialiser();
    }
  }

  /* Export pour les environnements à modules. L'affectation reste
     conditionnelle : le même fichier sert de script classique. */
  if (typeof module !== "undefined" && module.exports) {
    module.exports = Faso;
  }
})();
