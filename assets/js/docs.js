/* =============================================================
   Charte graphique de l'administration burkinabè
   docs.js — outillage du site de documentation

   Ne fait pas partie de la livraison aux projets.
   ============================================================= */

(function () {
  "use strict";

  function $(sel, racine) { return (racine || document).querySelector(sel); }
  function $$(sel, racine) { return Array.prototype.slice.call((racine || document).querySelectorAll(sel)); }

  /* =============================================================
     1. LECTURE DES FEUILLES DE STYLE

     Le CSS montré sous un exemple est lu dans les feuilles
     réellement chargées, jamais recopié à la main. Un navigateur
     n'autorise cette lecture que pour des feuilles de même
     origine : ouvrir les pages par double-clic (protocole
     file://) la bloque, les servir par HTTP l'autorise.
     ============================================================= */

  var FEUILLES_SYSTEME = ["tokens.css", "faso.css"];

  function feuillesLisibles() {
    var lisibles = [];
    var bloquee = false;

    Array.prototype.forEach.call(document.styleSheets, function (feuille) {
      var href = feuille.href || "";
      var nom = FEUILLES_SYSTEME.filter(function (f) { return href.indexOf(f) !== -1; })[0];
      if (!nom) return;

      try {
        if (feuille.cssRules) lisibles.push({ nom: nom, regles: feuille.cssRules });
      } catch (e) {
        bloquee = true;
      }
    });

    return { feuilles: lisibles, bloquee: bloquee };
  }

  /* Un sélecteur peut porter des pseudo-éléments et des pseudo-
     classes d'état qu'aucun élément au repos ne satisfait. On les
     retire pour le test d'appartenance, tout en conservant la
     règle d'origine dans le code affiché. */
  var PSEUDO_ETATS = [
    "focus-visible", "focus-within", "placeholder-shown", "out-of-range",
    "in-range", "read-only", "read-write", "indeterminate", "required",
    "optional", "disabled", "enabled", "checked", "default", "visited",
    "target", "autofill", "invalid", "active", "hover", "focus", "valid"
  ];

  function finParentheses(source, depart) {
    var niveau = 0;
    for (var i = depart; i < source.length; i++) {
      if (source[i] === "(") niveau++;
      else if (source[i] === ")") {
        niveau--;
        if (niveau === 0) return i + 1;
      }
    }
    return source.length;
  }

  /* Analyse caractère par caractère plutôt qu'expression
     régulière : il faut distinguer ce qui est au premier niveau
     de ce qui est argument d'un :not() ou d'un :has(). Retirer
     :disabled à l'intérieur d'un :not() produirait « :not() »,
     sélecteur invalide qui ferait perdre la règle. */
  function nettoyerPart(part) {
    var sortie = "";
    var parentheses = 0;
    var crochets = 0;
    var i = 0;

    while (i < part.length) {
      var c = part[i];

      if (c === "(") { parentheses++; sortie += c; i++; continue; }
      if (c === ")") { parentheses--; sortie += c; i++; continue; }
      if (c === "[") { crochets++; sortie += c; i++; continue; }
      if (c === "]") { crochets--; sortie += c; i++; continue; }

      if (c === ":" && parentheses === 0 && crochets === 0) {
        if (part[i + 1] === ":") {
          var fin = i + 2;
          while (fin < part.length && /[a-zA-Z-]/.test(part[fin])) fin++;
          if (part[fin] === "(") fin = finParentheses(part, fin);
          i = fin;
          continue;
        }

        var reste = part.slice(i + 1);
        var etat = null;

        for (var k = 0; k < PSEUDO_ETATS.length; k++) {
          var nom = PSEUDO_ETATS[k];
          if (reste.indexOf(nom) !== 0) continue;
          var suivant = reste.charAt(nom.length);
          if (suivant === "" || !/[a-zA-Z0-9_-]/.test(suivant)) { etat = nom; break; }
        }

        if (etat) { i += 1 + etat.length; continue; }
      }

      sortie += c;
      i++;
    }

    return sortie.trim();
  }

  function selecteurTestable(selecteur) {
    return selecteur.split(",").map(nettoyerPart).filter(Boolean).join(", ");
  }

  /* Le socle s'applique à tout : l'inclure ferait apparaître la
     même remise à zéro sous chacun des exemples. */
  var GENERIQUE = /^(\*|html|body|:root)$/;

  function regleConcerne(selecteur, elements) {
    var testable = selecteurTestable(selecteur);
    if (!testable) return false;

    var parts = testable.split(",").map(function (p) { return p.trim(); });
    if (parts.every(function (p) { return GENERIQUE.test(p); })) return false;

    try {
      return elements.some(function (el) { return el.matches(testable); });
    } catch (e) {
      return false;
    }
  }

  /* Mise en forme lisible : cssText rend tout sur une seule
     ligne, ce qui est illisible dans un panneau de code. */
  function formaterRegle(regle, retrait) {
    var pad = retrait || "";

    if (regle.type === CSSRule.MEDIA_RULE) {
      var condition = regle.conditionText || (regle.media && regle.media.mediaText) || "";
      var interieur = Array.prototype.map.call(regle.cssRules, function (r) {
        return formaterRegle(r, pad + "  ");
      }).filter(Boolean).join("\n\n");
      return pad + "@media " + condition + " {\n" + interieur + "\n" + pad + "}";
    }

    if (regle.type === CSSRule.KEYFRAMES_RULE) {
      return pad + regle.cssText;
    }

    if (!regle.style) return pad + regle.cssText;

    var lignes = [];
    for (var i = 0; i < regle.style.length; i++) {
      var prop = regle.style[i];
      var valeur = regle.style.getPropertyValue(prop).trim();
      var priorite = regle.style.getPropertyPriority(prop) ? " !important" : "";
      lignes.push(pad + "  " + prop + ": " + valeur + priorite + ";");
    }

    if (!lignes.length) return "";
    return pad + regle.selectorText + " {\n" + lignes.join("\n") + "\n" + pad + "}";
  }

  /* Table des jetons déclarés sur :root, pour pouvoir résoudre
     les var() rencontrées dans les règles retenues. */
  function tableJetons(feuilles) {
    var ordre = [];
    var valeurs = {};

    feuilles.forEach(function (f) {
      Array.prototype.forEach.call(f.regles, function (regle) {
        if (regle.type !== CSSRule.STYLE_RULE) return;
        if (regle.selectorText.indexOf(":root") === -1) return;
        if (regle.selectorText.indexOf("data-theme") !== -1) return;

        for (var i = 0; i < regle.style.length; i++) {
          var prop = regle.style[i];
          if (prop.indexOf("--") !== 0) continue;
          if (!(prop in valeurs)) ordre.push(prop);
          valeurs[prop] = regle.style.getPropertyValue(prop).trim();
        }
      });
    });

    return { ordre: ordre, valeurs: valeurs };
  }

  /* Résolution récursive : --action pointe sur --vert-500, qui
     porte la valeur finale. Les deux maillons sont restitués pour
     que l'extrait reste autonome. */
  function jetonsRequis(texte, table) {
    var requis = {};

    function explorer(source) {
      var trouve = source.match(/var\(\s*(--[\w-]+)/g);
      if (!trouve) return;

      trouve.forEach(function (brut) {
        var nom = brut.replace(/var\(\s*/, "");
        if (requis[nom] || !(nom in table.valeurs)) return;
        requis[nom] = true;
        explorer(table.valeurs[nom]);
      });
    }

    explorer(texte);

    return table.ordre.filter(function (nom) { return requis[nom]; });
  }

  function extraireCss(scene) {
    var etat = feuillesLisibles();

    if (!etat.feuilles.length) {
      return etat.bloquee
        ? "/* La lecture des feuilles de style est bloquée par le navigateur.\n"
          + "   Servez la documentation par HTTP pour afficher le CSS :\n"
          + "     python -m http.server 8000\n"
          + "   puis ouvrez http://localhost:8000/ */"
        : "/* Feuilles de style introuvables. */";
    }

    var elements = $$("*", scene);
    if (!elements.length) return "";

    var retenues = [];

    etat.feuilles.forEach(function (f) {
      Array.prototype.forEach.call(f.regles, function (regle) {
        if (regle.type === CSSRule.STYLE_RULE) {
          if (regle.selectorText.indexOf(":root") !== -1) return;
          if (regleConcerne(regle.selectorText, elements)) retenues.push(regle);
          return;
        }

        if (regle.type === CSSRule.MEDIA_RULE) {
          var pertinente = Array.prototype.some.call(regle.cssRules, function (r) {
            return r.type === CSSRule.STYLE_RULE && regleConcerne(r.selectorText, elements);
          });
          if (pertinente) retenues.push(regle);
        }
      });
    });

    if (!retenues.length) return "/* Aucune règle du système ne s'applique à cet exemple. */";

    var corps = retenues.map(function (r) { return formaterRegle(r, ""); })
                        .filter(Boolean)
                        .join("\n\n");

    /* Les animations référencées par les règles retenues doivent
       accompagner l'extrait, sinon il est incomplet. */
    var animations = [];
    etat.feuilles.forEach(function (f) {
      Array.prototype.forEach.call(f.regles, function (regle) {
        if (regle.type !== CSSRule.KEYFRAMES_RULE) return;
        if (corps.indexOf(regle.name) !== -1) animations.push(formaterRegle(regle, ""));
      });
    });

    var table = tableJetons(etat.feuilles);
    var noms = jetonsRequis(corps, table);

    var blocs = [];

    if (noms.length) {
      blocs.push(
        ":root {\n"
        + noms.map(function (n) { return "  " + n + ": " + table.valeurs[n] + ";"; }).join("\n")
        + "\n}"
      );
    }

    blocs.push(corps);
    if (animations.length) blocs.push(animations.join("\n\n"));

    return blocs.join("\n\n");
  }

  /* =============================================================
     2. COMPORTEMENTS JAVASCRIPT ASSOCIÉS

     Chaque exemple est rapproché des fonctions de faso.js qui
     l'animent, d'après les points d'accroche présents dans son
     balisage. La source affichée est lue par toString() : c'est
     exactement le code qui tourne dans la page.
     ============================================================= */

  var ACCROCHES = [
    { selecteur: '[role="tablist"]',                          cle: "onglets" },
    { selecteur: '[data-ouvre-modale]',                       cle: "modales" },
    { selecteur: 'dialog.fs-modale',                          cle: "modalesLocales" },
    { selecteur: '[data-ouvre-panneau], .fs-panneau-lateral', cle: "panneaux" },
    { selecteur: '[data-menu]',                               cle: "menus" },
    { selecteur: '.fs-depot',                                 cle: "depots" },
    { selecteur: '.fs-code',                                  cle: "codes" },
    { selecteur: '[data-triable]',                            cle: "tris" },
    { selecteur: '[data-pagination]',                         cle: "paginations" },
    { selecteur: '[data-filtres]',                            cle: "filtres" },
    { selecteur: '[data-export]',                             cle: "exports" },
    { selecteur: '[data-ferme-encart]',                       cle: "encarts" },
    { selecteur: '.fs-notifications',                         cle: "centreNotifications" },
    { selecteur: '.fs-calendrier',                            cle: "calendriers" },
    { selecteur: '[data-compteur]',                           cle: "compteurs" },
    { selecteur: '.fs-jauge[data-valeur]',                    cle: "jauges" },
    { selecteur: '.fs-date',                                  cle: "dates" },
    { selecteur: '[data-combo]',                              cle: "combos" },
    { selecteur: '.fs-barres[data-max]',                      cle: "barres" },
    { selecteur: '[data-bascule]',                            cle: "navigation" },
    { selecteur: '[data-bascule-theme]',                      cle: "theme" },
    { selecteur: '[onclick*="Faso.notifier"]',                cle: "notifier" }
  ];

  function extraireJs(scene) {
    var comportements = window.Faso && window.Faso.comportements;
    if (!comportements) return "";

    var sources = [];

    ACCROCHES.forEach(function (accroche) {
      var present;
      try {
        present = scene.querySelector(accroche.selecteur) !== null;
      } catch (e) {
        present = false;
      }
      if (!present) return;

      var fn = comportements[accroche.cle];
      if (!fn || sources.indexOf(fn) !== -1) return;
      sources.push(fn);
    });

    if (!sources.length) return "";

    return sources.map(function (fn) { return fn.toString(); }).join("\n\n");
  }

  /* =============================================================
     3. PANNEAU DE CODE

     Un seul bouton au repos, afin de ne pas alourdir la page. Les
     onglets et la copie n'apparaissent qu'une fois le panneau
     ouvert, et le contenu n'est calculé qu'à ce moment.
     ============================================================= */

  function normaliser(html) {
    var lignes = html.replace(/\t/g, "  ").split("\n");

    while (lignes.length && !lignes[0].trim()) lignes.shift();
    while (lignes.length && !lignes[lignes.length - 1].trim()) lignes.pop();
    if (!lignes.length) return "";

    var retrait = lignes.reduce(function (min, ligne) {
      if (!ligne.trim()) return min;
      return Math.min(min, ligne.match(/^ */)[0].length);
    }, Infinity);

    return lignes.map(function (ligne) { return ligne.slice(retrait); }).join("\n");
  }

  function initExemples() {
    $$(".doc-demo").forEach(function (demo, index) {
      var scene = $(".doc-demo-scene", demo);
      if (!scene || $(".doc-code", demo)) return;

      /* Le HTML est relevé tout de suite : faso.js s'exécute
         après docs.js et ajoute des attributs au balisage. */
      var html = normaliser(scene.innerHTML);
      if (!html) return;

      var idPanneau = "code-" + index;
      var contenus = { html: html, css: null, js: null };
      var langue = "html";

      var barre = document.createElement("div");
      barre.className = "doc-demo-barre";

      var libelle = document.createElement("span");
      libelle.className = "doc-demo-libelle";
      libelle.textContent = demo.getAttribute("data-libelle") || "Exemple";

      var declencheur = document.createElement("button");
      declencheur.type = "button";
      declencheur.className = "doc-copie";
      declencheur.setAttribute("aria-expanded", "false");
      declencheur.setAttribute("aria-controls", idPanneau);
      declencheur.textContent = "Code";

      barre.appendChild(libelle);
      barre.appendChild(declencheur);

      var panneau = document.createElement("div");
      panneau.className = "doc-code";
      panneau.id = idPanneau;
      panneau.hidden = true;

      var outils = document.createElement("div");
      outils.className = "doc-code-barre";

      var onglets = document.createElement("div");
      onglets.className = "doc-code-onglets";

      var copier = document.createElement("button");
      copier.type = "button";
      copier.className = "doc-copie";
      copier.textContent = "Copier";

      outils.appendChild(onglets);
      outils.appendChild(copier);

      var pre = document.createElement("pre");
      pre.className = "doc-demo-code";
      var balise = document.createElement("code");
      pre.appendChild(balise);

      panneau.appendChild(outils);
      panneau.appendChild(pre);

      demo.appendChild(barre);
      demo.appendChild(panneau);

      var boutons = {};

      function afficher(cle) {
        langue = cle;
        balise.textContent = contenus[cle] || "";
        Object.keys(boutons).forEach(function (k) {
          boutons[k].setAttribute("aria-pressed", String(k === cle));
        });
      }

      function ajouterOnglet(cle, texte) {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.setAttribute("aria-pressed", "false");
        btn.textContent = texte;
        btn.addEventListener("click", function () { afficher(cle); });
        onglets.appendChild(btn);
        boutons[cle] = btn;
      }

      /* Le calcul du CSS et du JS est différé au premier
         déploiement : sur une page portant quarante exemples, le
         faire au chargement serait inutilement coûteux. */
      var calcule = false;

      function preparer() {
        if (calcule) return;
        calcule = true;

        contenus.css = extraireCss(scene);
        contenus.js = extraireJs(scene);

        ajouterOnglet("html", "HTML");
        if (contenus.css) ajouterOnglet("css", "CSS");
        if (contenus.js) ajouterOnglet("js", "JS");

        afficher("html");
      }

      declencheur.addEventListener("click", function () {
        preparer();
        panneau.hidden = !panneau.hidden;
        declencheur.setAttribute("aria-expanded", String(!panneau.hidden));
        declencheur.textContent = panneau.hidden ? "Code" : "Masquer";
      });

      copier.addEventListener("click", function () {
        var fini = function (message) {
          copier.textContent = message;
          setTimeout(function () { copier.textContent = "Copier"; }, 2000);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(contenus[langue] || "").then(
            function () { fini("Copié"); },
            function () { fini("Échec"); }
          );
        } else {
          fini("Indisponible");
        }
      });
    });
  }

  /* -----------------------------------------------------------
     Copie d'une valeur de jeton au clic
     ----------------------------------------------------------- */
  function initCopieValeur() {
    document.addEventListener("click", function (ev) {
      var cible = ev.target.closest("[data-copier]");
      if (!cible || !navigator.clipboard) return;

      navigator.clipboard.writeText(cible.getAttribute("data-copier")).then(function () {
        if (window.Faso) {
          window.Faso.notifier({
            titre: "Valeur copiée",
            texte: cible.getAttribute("data-copier"),
            ton: "succes",
            duree: 2500
          });
        }
      });
    });
  }

  /* -----------------------------------------------------------
     Sommaire actif au défilement
     ----------------------------------------------------------- */
  function initSommaire() {
    var sommaire = $(".fs-sommaire");
    if (!sommaire || !("IntersectionObserver" in window)) return;

    var liens = $$("a[href^='#']", sommaire);
    if (!liens.length) return;

    var parId = {};
    var sections = [];

    liens.forEach(function (lien) {
      var section = document.getElementById(lien.getAttribute("href").slice(1));
      if (!section) return;
      parId[section.id] = lien;
      sections.push(section);
    });

    var visibles = new Set();

    var observateur = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (entree.isIntersecting) visibles.add(entree.target.id);
        else visibles.delete(entree.target.id);
      });

      /* La section active est la première visible dans l'ordre
         du document, pour éviter que le repère saute vers le bas
         de page pendant un défilement rapide. */
      var actif = sections.filter(function (s) { return visibles.has(s.id); })[0];

      liens.forEach(function (lien) { lien.removeAttribute("aria-current"); });
      if (actif && parId[actif.id]) parId[actif.id].setAttribute("aria-current", "true");
    }, { rootMargin: "-80px 0px -60% 0px", threshold: 0 });

    sections.forEach(function (section) { observateur.observe(section); });
  }

  /* -----------------------------------------------------------
     Construction du sommaire à partir des titres de section
     ----------------------------------------------------------- */
  function initSommaireAuto() {
    var hote = $("[data-sommaire-auto]");
    if (!hote) return;

    var sections = $$(".doc-section[id]");
    if (!sections.length) return;

    var liste = document.createElement("ul");

    sections.forEach(function (section) {
      var titre = $("h2", section);
      if (!titre) return;

      var li = document.createElement("li");
      var a = document.createElement("a");
      a.href = "#" + section.id;
      a.textContent = titre.textContent.trim();
      li.appendChild(a);
      liste.appendChild(li);
    });

    hote.appendChild(liste);
  }

  /* -----------------------------------------------------------
     Repère de contraste

     Calcul du rapport selon WCAG 2.1, pour vérifier la palette
     dans la documentation plutôt que dans un outil tiers.
     ----------------------------------------------------------- */
  function versRVB(hex) {
    var v = hex.replace("#", "");
    if (v.length === 3) v = v.split("").map(function (c) { return c + c; }).join("");
    return [
      parseInt(v.slice(0, 2), 16),
      parseInt(v.slice(2, 4), 16),
      parseInt(v.slice(4, 6), 16)
    ];
  }

  function luminance(rvb) {
    var canaux = rvb.map(function (valeur) {
      var c = valeur / 255;
      return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * canaux[0] + 0.7152 * canaux[1] + 0.0722 * canaux[2];
  }

  function contraste(hexA, hexB) {
    var la = luminance(versRVB(hexA));
    var lb = luminance(versRVB(hexB));
    return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
  }

  function initContrastes() {
    $$("[data-contraste]").forEach(function (element) {
      var paire = element.getAttribute("data-contraste").split("/");
      if (paire.length !== 2) return;

      var rapport = contraste(paire[0].trim(), paire[1].trim());

      var niveau, classe;
      if (rapport >= 7) { niveau = "AAA"; classe = "doc-contraste--ok"; }
      else if (rapport >= 4.5) { niveau = "AA"; classe = "doc-contraste--ok"; }
      else if (rapport >= 3) { niveau = "AA grand"; classe = "doc-contraste--moyen"; }
      else { niveau = "Insuffisant"; classe = "doc-contraste--non"; }

      element.classList.add("doc-contraste", classe);
      element.textContent = (Math.round(rapport * 100) / 100).toFixed(2) + " : 1 — " + niveau;
    });
  }

  function initialiser() {
    initExemples();
    initCopieValeur();
    initSommaireAuto();
    initSommaire();
    initContrastes();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initialiser);
  } else {
    initialiser();
  }
})();
