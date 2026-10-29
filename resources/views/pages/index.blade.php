@extends('layouts.accueil')

@section('contenu')

  <section class="doc-heros">
    <div class="fs-conteneur">
      <div>
        <span class="fs-surtitre">Version 3.0&nbsp;— Octobre 2026</span>
        <h1>Une identité commune pour tous les <span class="doc-accent">services numériques</span> de l'État</h1>
        <p class="fs-chapeau">
          Cette charte fixe les fondations visuelles, la bibliothèque de composants et
          les gabarits de référence des plateformes publiques burkinabè. Un usager doit
          reconnaître immédiatement un service de l'État, y accéder depuis un téléphone
          d'entrée de gamme, et comprendre ce qu'on attend de lui.
        </p>

        <div class="fs-actions">
          <a class="fs-btn fs-btn--principal fs-btn--lg" href="{{ page('fondations') }}">Ouvrir les fondations</a>
          <a class="fs-btn fs-btn--secondaire fs-btn--lg" href="{{ page('composants') }}">Parcourir les composants</a>
        </div>
      </div>

@unless (config('charte.export'))
{{-- La recherche demande un serveur : le site exporté, statique, ne la
     propose pas, et garde le drapeau seul dans sa colonne. --}}
      <div class="doc-heros-cote">
        @include('partials.recherche', ['id' => 'recherche-accueil', 'libelleMasque' => true])
@endunless
      <!-- Une pièce de Faso Dan Fani, l'étoffe tissée en bandes étroites
           cousues bord à bord, rayée indigo, écru et or ; par-dessus, une
           carte rouge et verte portant l'étoile. Ce n'est pas le drapeau,
           c'est ce qu'il inspire. -->
      <svg class="doc-heros-art" viewBox="0 0 520 520" aria-hidden="true" focusable="false">
        <defs>
          <!-- Une bande de 96 unités : rayures de chaîne, couture à gauche,
               une ligne de trame claire toutes les deux unités. -->
          <pattern id="tissage" width="96" height="4" patternUnits="userSpaceOnUse">
            <rect x="0" width="14" height="4" fill="#22305a"/>
            <rect x="14" width="3" height="4" fill="#efe6d2"/>
            <rect x="17" width="3" height="4" fill="#22305a"/>
            <rect x="20" width="2" height="4" fill="#fcd116"/>
            <rect x="22" width="3" height="4" fill="#22305a"/>
            <rect x="25" width="12" height="4" fill="#efe6d2"/>
            <rect x="37" width="22" height="4" fill="#22305a"/>
            <rect x="59" width="12" height="4" fill="#efe6d2"/>
            <rect x="71" width="3" height="4" fill="#22305a"/>
            <rect x="74" width="2" height="4" fill="#fcd116"/>
            <rect x="76" width="3" height="4" fill="#22305a"/>
            <rect x="79" width="3" height="4" fill="#efe6d2"/>
            <rect x="82" width="14" height="4" fill="#22305a"/>
            <rect width="96" height="1" fill="#fff" fill-opacity="0.14"/>
            <rect y="2" width="96" height="1" fill="#fff" fill-opacity="0.14"/>
            <rect width="1" height="4" fill="#000" fill-opacity="0.35"/>
          </pattern>
          <clipPath id="carte">
            <rect x="96" y="60" width="328" height="400" rx="34"/>
          </clipPath>
          <filter id="ombre" x="-20%" y="-20%" width="140%" height="150%">
            <feDropShadow dx="0" dy="18" stdDeviation="16" flood-color="#032e16" flood-opacity="0.22"/>
          </filter>
        </defs>

        <rect x="40" y="110" width="440" height="380" rx="40" fill="url(#tissage)"
              transform="rotate(-7 260 300)" opacity="0.95"/>

        <g transform="rotate(5 260 260)" filter="url(#ombre)">
          <g clip-path="url(#carte)">
            <rect x="96" y="60" width="328" height="200" fill="#ef2b2d"/>
            <rect x="96" y="260" width="328" height="200" fill="#009e49"/>
          </g>
          <rect x="96" y="60" width="328" height="400" rx="34" fill="none" stroke="#fff" stroke-opacity="0.35" stroke-width="2"/>
          <polygon fill="#fcd116"
                   points="260,190 275.9,238.2 326.6,238.4 285.7,268.3 301.1,316.6 260,287 218.9,316.6 234.3,268.3 193.4,238.4 244.1,238.2"/>
        </g>
      </svg>
@unless (config('charte.export'))
      </div>
@endunless
    </div>
  </section>

  <section class="fs-fond-basse">
    <div class="fs-conteneur">
      <div class="doc-chiffres">
        <div>
          <span class="doc-chiffre-valeur">66</span>
          <span class="doc-chiffre-libelle">composants documentés</span>
        </div>
        <div>
          <span class="doc-chiffre-valeur">7</span>
          <span class="doc-chiffre-libelle">familles d'interface</span>
        </div>
        <div>
          <span class="doc-chiffre-valeur">3</span>
          <span class="doc-chiffre-libelle">couleurs nationales, 30 paliers</span>
        </div>
        <div>
          <span class="doc-chiffre-valeur">AA</span>
          <span class="doc-chiffre-libelle">niveau de contraste vérifié</span>
        </div>
      </div>
    </div>
  </section>

  <section class="fs-section">
    <div class="fs-conteneur">
      <div class="doc-accueil-titre">
        <h2>Ce que contient la charte</h2>
        <p class="fs-chapeau">
          Du plus abstrait au plus concret. Chaque niveau s'appuie sur le précédent et
          n'introduit jamais de valeur nouvelle.
        </p>
      </div>

      <div class="doc-entrees">
        <a class="doc-entree" href="{{ page('fondations') }}">
          <span class="doc-entree-num">01&nbsp;— Fondations</span>
          <span class="doc-entree-titre">Emblème, couleurs, typographie</span>
          <span class="doc-entree-texte">
            Le bloc-marque et ses règles, les trois couleurs nationales déclinées en
            échelles tonales, la grille, l'espacement, la profondeur et le mouvement.
          </span>
          <span class="doc-entree-compte">Palette · Typographie · Grille · Formes · Motif tissé</span>
        </a>

        <a class="doc-entree doc-entree--or" href="{{ page('composants') }}">
          <span class="doc-entree-num">02&nbsp;— Composants de base</span>
          <span class="doc-entree-titre">Sept familles, HTML et CSS natifs</span>
          <span class="doc-entree-texte">
            Les briques communes à tous les services, sans dépendance ni compilation.
            Chaque exemple donne son HTML, son CSS et son JS réels.
          </span>
          <span class="doc-entree-compte">Actions · Formulaires · Navigation · Information · Données · Retour · Superposition</span>
        </a>

        <a class="doc-entree doc-entree--rouge" href="{{ page('composants-metier') }}">
          <span class="doc-entree-num">03&nbsp;— Composants métier</span>
          <span class="doc-entree-titre">Ce qui n'existe que dans le service public</span>
          <span class="doc-entree-texte">
            Identification officielle, fiche de démarche, pièces à fournir, suivi de
            dossier, signalement de fraude, canaux de paiement autorisés.
          </span>
          <span class="doc-entree-compte">Confiance numérique · Parcours administratif</span>
        </a>

        <a class="doc-entree" href="{{ page('gabarits') }}">
          <span class="doc-entree-num">04&nbsp;— Gabarits</span>
          <span class="doc-entree-titre">Les assemblages de référence</span>
          <span class="doc-entree-texte">
            Portail de services, résultats de recherche, contenu institutionnel,
            fiche de démarche, formulaire long, tableaux de bord usager et agent,
            authentification et pages d'erreur.
          </span>
          <span class="doc-entree-compte">Neuf gabarits validés</span>
        </a>

        <a class="doc-entree doc-entree--or" href="{{ page('accessibilite') }}">
          <span class="doc-entree-num">05&nbsp;— Accessibilité</span>
          <span class="doc-entree-titre">Les exigences non négociables</span>
          <span class="doc-entree-texte">
            Contrastes vérifiés en direct, navigation au clavier, cibles tactiles,
            contrainte réseau, messages d'erreur et ton administratif.
          </span>
          <span class="doc-entree-compte">WCAG 2.1 AA · Rédaction</span>
        </a>

        <a class="doc-entree doc-entree--rouge" href="{{ page('gouvernance') }}">
          <span class="doc-entree-num">06&nbsp;— Gouvernance</span>
          <span class="doc-entree-titre">Qui décide, et comment ça évolue</span>
          <span class="doc-entree-texte">
            Rôles, versionnement, cycle de vie d'un composant, procédure de
            contribution, dérogations et journal des versions.
          </span>
          <span class="doc-entree-compte">Rôles · Versions · Dérogations · Conformité</span>
        </a>

        <a class="doc-entree" href="#principes">
          <span class="doc-entree-num">07&nbsp;— Doctrine</span>
          <span class="doc-entree-titre">Six principes pour trancher</span>
          <span class="doc-entree-texte">
            Les arbitrages qui fondent le système et permettent de décider dans les cas
            que la documentation ne prévoit pas.
          </span>
          <span class="doc-entree-compte">Principes directeurs</span>
        </a>
      </div>
    </div>
  </section>

  <section class="fs-section fs-fond-enfoncee" id="principes">
    <div class="fs-conteneur">
      <div class="doc-accueil-titre">
        <h2>Six principes directeurs</h2>
        <p class="fs-chapeau">
          Une charte ne peut pas tout prévoir. Lorsque la documentation reste muette,
          ces principes tranchent.
        </p>
      </div>

      <div class="fs-auto-grille" style="--min-colonne: 300px;">
        <article class="doc-principe">
          <span class="doc-principe-num" aria-hidden="true">1</span>
          <h3>Le symbole avant l'ornement</h3>
          <p>
            Les armoiries, les trois couleurs et la devise ne sont pas des éléments
            décoratifs mobilisables librement. Leur emploi identifie l'État et obéit
            aux règles de la page Fondations.
          </p>
        </article>

        <article class="doc-principe">
          <span class="doc-principe-num" aria-hidden="true">2</span>
          <h3>La reconnaissance avant l'esthétique</h3>
          <p>
            Un usager doit distinguer un service authentique d'une contrefaçon avant
            de saisir quoi que ce soit. Le bandeau officiel, le domaine visible et l'autorité
            responsable priment sur toute recherche graphique.
          </p>
        </article>

        <article class="doc-principe">
          <span class="doc-principe-num" aria-hidden="true">3</span>
          <h3>La contrainte réseau d'abord</h3>
          <p>
            Les interfaces sont conçues pour un téléphone modeste sur réseau instable.
            Un composant qui exige JavaScript pour afficher une information essentielle
            est un composant mal conçu.
          </p>
        </article>

        <article class="doc-principe">
          <span class="doc-principe-num" aria-hidden="true">4</span>
          <h3>L'information avant le formulaire</h3>
          <p>
            Coût, délai, pièces à fournir et administration responsable s'affichent
            avant le premier champ de saisie. L'usager s'engage en connaissance de
            cause.
          </p>
        </article>

        <article class="doc-principe">
          <span class="doc-principe-num" aria-hidden="true">5</span>
          <h3>La couleur ne porte jamais seule</h3>
          <p>
            Tout état signalé par une couleur l'est aussi par un texte, une forme ou
            un repère. Un daltonien et un écran monochrome lisent la même
            information.
          </p>
        </article>

        <article class="doc-principe">
          <span class="doc-principe-num" aria-hidden="true">6</span>
          <h3>Une seule source pour chaque valeur</h3>
          <p>
            Aucune couleur, aucune taille, aucun rayon n'est écrit en dur dans un
            composant&nbsp;: tout passe par un jeton, et une décision se change à un
            seul endroit.
          </p>
        </article>
      </div>
    </div>
  </section>

  <section class="fs-section">
    <div class="fs-conteneur">
      <div class="fs-avec-aside">
        <div>
          <span class="fs-surtitre">Mise en œuvre</span>
          <h2>Intégrer la charte dans un projet</h2>

          <p class="fs-mesure">
            Le système est livré en HTML et CSS natifs, sans dépendance ni étape de
            compilation. Trois feuilles de style&nbsp;: le socle de jetons, la
            bibliothèque de composants, puis le jeu d'icônes. Le fichier JavaScript
            est facultatif et n'active que les comportements qui ne peuvent pas être
            obtenus en CSS.
          </p>

          <pre><code>&lt;link rel="stylesheet" href="assets/css/tokens.css"&gt;
&lt;link rel="stylesheet" href="assets/css/faso.css"&gt;
&lt;link rel="stylesheet" href="assets/css/icones.css"&gt;
&lt;script src="assets/js/faso.js" defer&gt;&lt;/script&gt;</code></pre>

          <p class="fs-legende fs-mt-4">
            L'ordre des deux premières feuilles est imposé. <code>icones.css</code>
            peut être placé avant ou après <code>faso.css</code>&nbsp;: les variables
            CSS se résolvent à l'affichage, pas à la lecture du fichier. Il reste
            obligatoire&nbsp;: plusieurs composants y puisent leurs icônes.
          </p>

          <h3 class="fs-mt-8">Règles d'intégration</h3>
          <ul class="fs-liste-puces fs-mesure">
            <li>
              Ne jamais modifier <code>faso.css</code> dans un projet. Les adaptations
              passent par une surcharge de jetons dans une feuille propre au projet.
            </li>
            <li>
              Conserver le préfixe <code>fs-</code> pour les classes du système et
              utiliser un préfixe distinct pour les classes spécifiques au projet.
            </li>
            <li>
              Le bloc-marque et le bandeau d'identification officielle sont repris
              tels quels, sans adaptation de proportions ni de libellés.
            </li>
            <li>
              Toute dérogation doit être documentée et soumise à l'équipe en charge
              du système avant mise en production.
            </li>
          </ul>

          <p class="fs-mesure fs-mt-6">
            La charte applique le «&nbsp;Guide pour la définition de la charte graphique
            des plateformes numériques de l'administration publique&nbsp;» (MTDPCE,
            version V1.0 du 10/08/2024)&nbsp;: couleurs officielles, Montserrat et
            Roboto, palettes d'espace, structure des pages. Les points qu'elle
            n'applique pas tels quels sont
            <a href="{{ page('gouvernance') }}#ecarts-guide">listés et motivés dans la page Gouvernance</a>.
          </p>

          <p class="fs-mesure fs-mt-6">
            Toute la charte existe aussi en un seul fichier Markdown, pour les
            développeurs et les agents de code qui les assistent&nbsp;:
            <a href="{{ markdown() }}" download="{{ \App\Support\CharteMarkdown::nomFichier() }}">{{ \App\Support\CharteMarkdown::nomFichier() }}</a>.
            Il s'ouvre sur les consignes que tout agent doit suivre&nbsp;; son emploi est
            décrit dans la page <a href="{{ page('integration') }}#markdown">Intégration</a>.
          </p>

          <div class="fs-encart fs-encart--info fs-mt-8">
            <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
            <div class="fs-encart-corps">
              <p class="fs-encart-titre">Compatibilité</p>
              <p>
                Le système s'appuie sur les propriétés personnalisées CSS, la grille,
                <code>:has()</code> et l'élément <code>&lt;dialog&gt;</code>. Sur un
                navigateur plus ancien, la mise en page reste lisible et les contenus
                restent accessibles&nbsp;; seuls les raffinements visuels sont perdus.
              </p>
            </div>
          </div>
        </div>

        <aside>
          <div class="fs-carte fs-carte--marquee">
            <div class="fs-carte-corps">
              <h3 class="fs-carte-titre" style="font-size: var(--taille-md);">Ressources</h3>
              <ul class="fs-liste-nue" style="font-size: var(--taille-sm);">
                <li style="margin-bottom: var(--espace-3);">
                  <a class="fs-lien-fichier" href="{{ ressource('assets/jetons.json') }}" download>
                    Jetons du système
                    <span class="fs-poids">JSON&nbsp;— 14&nbsp;Ko</span>
                  </a>
                  <br><span class="fs-faible">202 jetons, valeurs résolues et surcharges du thème sombre</span>
                </li>
                <li style="margin-bottom: var(--espace-3);">
                  <a class="fs-lien-fichier" href="{{ ressource('assets/img/icones.svg') }}" download>
                    Jeu d'icônes
                    <span class="fs-poids">SVG&nbsp;— 8&nbsp;Ko</span>
                  </a>
                  <br><span class="fs-faible">35 tracés, grille de 24&nbsp;px</span>
                </li>
                <li>
                  <a class="fs-lien-fichier" href="{{ ressource('assets/img/armoiries.svg') }}" download>
                    Emblème vectoriel
                    <span class="fs-poids">SVG&nbsp;— 191&nbsp;Ko</span>
                  </a>
                  <br><span class="fs-faible">Version de travail&nbsp;; la référence est délivrée par le SGG</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="fs-carte fs-mt-6">
            <div class="fs-carte-corps">
              <h3 class="fs-carte-titre" style="font-size: var(--taille-md);">Fichiers livrés</h3>
              <ul class="fs-liste-separee" style="font-size: var(--taille-sm);">
                <li><strong>tokens.css</strong><br><span class="fs-faible">Jetons et thèmes</span></li>
                <li><strong>faso.css</strong><br><span class="fs-faible">Bibliothèque de composants</span></li>
                <li><strong>icones.css</strong><br><span class="fs-faible">35 icônes en masques CSS</span></li>
                <li><strong>faso.js</strong><br><span class="fs-faible">Comportements, facultatif</span></li>
                <li><strong>icones.svg</strong><br><span class="fs-faible">Mêmes tracés, pour les outils de conception</span></li>
                <li><strong>armoiries.svg</strong><br><span class="fs-faible">Emblème vectoriel, 45 Ko compressé</span></li>
              </ul>
            </div>
          </div>

          <div class="fs-encart fs-encart--info fs-mt-6">
            <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
            <div class="fs-encart-corps">
              <p class="fs-encart-titre">Périmètre de la charte</p>
              <p>
                Elle couvre les <strong>services numériques accessibles par
                navigateur</strong>. Elle ne traite pas l'identité imprimée&nbsp;:
                papeterie, signalétique, gabarits bureautiques.
              </p>
              <p>
                Les composants ne s'exécutent pas dans une application native&nbsp;;
                ce qui s'y transporte, ce sont les jetons, l'emblème et les marques
                nationales. La charte fournit des ressources Android générées depuis
                <code>tokens.css</code>, pour Jetpack Compose et les vues XML, et
                <code>jetons.json</code> sert de source pour les autres plateformes.
                Voir la page
                <a href="{{ page('integration') }}#android">Intégration</a>.
              </p>
              <p>
                Le <strong>français</strong> est la seule langue servie par cette
                version. Une part de la population ne le lit pas. Les canaux
                d'accueil physique et téléphonique restent donc indispensables, et
                aucun service ne peut être fermé au profit du seul canal en ligne.
              </p>
            </div>
          </div>

          <div class="fs-encart fs-encart--alerte fs-mt-6">
            <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
            <div class="fs-encart-corps">
              <p class="fs-encart-titre">Emblème officiel</p>
              <p>
                L'emblème fourni est vectoriel et convient à la conception comme au
                développement. Il reste dérivé d'un rendu raster&nbsp;: pour une mise en
                production, la version de référence doit être obtenue auprès du
                Secrétariat général du Gouvernement.
                <a href="{{ page('fondations') }}#marque">Voir les formats et les tailles</a>.
              </p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

@endsection
