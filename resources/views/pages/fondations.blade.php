@extends('layouts.documentation')

@section('contenu')

    <div class="doc-entete">
      <nav class="doc-fil" aria-label="Fil d'Ariane">
        <a href="{{ page('index') }}">Charte</a> <span aria-hidden="true">/</span> <span>Fondations</span>
      </nav>
      <h1>Fondations</h1>
      <p class="fs-chapeau">
        Les fondations sont les décisions dont tout le reste découle. Un composant
        ne définit pas une couleur, une taille ou un rayon. Il consomme un jeton
        déclaré ici. On fait donc évoluer la charte sans réécrire les composants.
      </p>
    </div>

    <!-- ===================================================== -->
    <section class="doc-section" id="marque">
      <h2>Emblème et bloc-marque</h2>
      <p class="doc-section-intro">
        Les armoiries du Burkina Faso sont un symbole de souveraineté.
        Leur emploi identifie l'État et engage la responsabilité de l'administration
        qui les affiche. Les règles qui suivent sont impératives.
      </p>

      <div class="doc-bloc">
        <h3>Bloc-marque officiel <code>.fs-marque</code></h3>
        <p class="doc-bloc-note">
          Le bloc-marque associe l'emblème, la dénomination de l'État, la devise
          nationale et, le cas échéant, l'entité responsable du service. Il est le
          premier élément de contenu de toute page, en haut à gauche, et c'est un
          lien&nbsp;: il renvoie à la page d'accueil du site, ou à celle de l'espace
          privé lorsque l'usager s'y trouve (guide, §&nbsp;2.1.1).
        </p>

        <div class="doc-demo" data-libelle="Bloc-marque complet">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <a class="fs-marque" href="#">
              <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="Armoiries du Burkina Faso">
              <span class="fs-marque-texte">
                <span class="fs-marque-pays">Burkina Faso</span>
                <span class="fs-marque-devise">La Patrie ou la Mort, nous Vaincrons</span>
                <span class="fs-marque-entite">Ministère de l'Économie et des Finances</span>
              </span>
            </a>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Place et dimensions du logo</h3>
        <p class="doc-bloc-note">
          Pour les ministères et les institutions, le logo par défaut des plateformes
          est l'emblème du Burkina Faso. Le guide en fixe la place et l'encombrement
          (guide, §&nbsp;2.1.1, figure&nbsp;1)&nbsp;; <code>.fs-marque</code> et
          <code>.fs-entete-haut</code> les appliquent sans réglage.
        </p>

        <dl class="fs-definitions">
          <div>
            <dt>Position</dt>
            <dd>En haut à gauche, sur toutes les pages et dans tous les espaces de la plateforme.</dd>
          </div>
          <div>
            <dt>Lien</dt>
            <dd>Toujours cliquable&nbsp;: retour à l'accueil du site, ou à l'accueil de l'espace privé depuis cet espace.</dd>
          </div>
          <div>
            <dt>Cadre</dt>
            <dd>76&nbsp;×&nbsp;80&nbsp;px, à 9&nbsp;px du haut de la page. L'emblème, plus haut que large (468&nbsp;×&nbsp;550), y occupe toute la hauteur&nbsp;: 80&nbsp;px, soit environ 68&nbsp;px de large. Sous 640&nbsp;px de largeur de fenêtre, la charte le ramène à 44&nbsp;px, adaptation propre à l'écran étroit que le guide ne traite pas.</dd>
          </div>
          <div>
            <dt>Version</dt>
            <dd>Les armoiries dans leur version officielle, avec l'encadré, et en bonne qualité&nbsp;: le fichier vectoriel, jamais une capture agrandie.</dd>
          </div>
          <div>
            <dt>Drapeau</dt>
            <dd>S'il accompagne les armoiries, le drapeau est placé avant elles, à la place du logo.</dd>
          </div>
        </dl>

        <div class="doc-note fs-mt-6">
          <strong>Logo propre à une structure.</strong> Une structure, ou un service
          d'une structure, peut employer un logo qui lui est propre. Le guide
          l'autorise, mais à une condition&nbsp;: toute la charte visuelle doit alors
          être repensée pour être en harmonie avec ce logo (guide, §&nbsp;2.1.2).
          Substituer un logo dans le bloc-marque en gardant le reste de cette charte
          ne remplit pas cette condition. Les couleurs par défaut du logo sont celles
          du drapeau.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Formats de l'emblème</h3>
        <p class="doc-bloc-note">
          L'emblème complet cesse d'être lisible bien avant le favicon. Ses onze
          tracés, ses deux chevaux et sa devise demandent de la place. En dessous de
          64&nbsp;px il perd ses détails, en dessous de 32&nbsp;px il n'est plus qu'une
          tache. Le système livre donc <strong>quatre formats gradués</strong>, chacun
          avec sa plage d'emploi, plus une version en niveaux de gris et des
          exports matriciels.
        </p>

        <p class="doc-bloc-note">
          Tous sont vectoriels. Le fichier de référence pèse 45&nbsp;Ko une fois
          compressé, contre 916&nbsp;Ko pour le raster dont il est issu, soit vingt fois
          moins sur une connexion facturée au volume.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Les quatre formats, du plus complet au plus réduit</caption>
            <thead>
              <tr><th>Format</th><th>Fichier</th><th>Plage d'emploi</th><th>Ce qu'il montre</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Grand</strong></td>
                <td><code>armoiries.svg</code><br><span class="fs-faible">191&nbsp;Ko</span></td>
                <td>À partir de 64&nbsp;px&nbsp;: en-tête (80&nbsp;px), page d'accueil, impression, affiche</td>
                <td>L'emblème complet&nbsp;: écu, chevaux, lances, banderoles et devise.</td>
              </tr>
              <tr>
                <td><strong>Moyen</strong></td>
                <td><code>armoiries-moyen.svg</code><br><span class="fs-faible">118&nbsp;Ko</span></td>
                <td>32 à 64&nbsp;px&nbsp;: en-tête sur écran étroit (44&nbsp;px), pied de page</td>
                <td>Le même dessin, géométrie allégée d'une passe. Indiscernable de la
                    référence à ces tailles, un tiers plus léger.</td>
              </tr>
              <tr>
                <td><strong>Petit</strong></td>
                <td><code>armoiries-ecu.svg</code><br><code>armoiries-ecu-32.png</code></td>
                <td>16 à 32&nbsp;px, liste, pastille</td>
                <td>L'écu seul, découpé suivant sa silhouette. Le drapeau et l'étoile
                    restent lisibles là où les chevaux ne le sont plus.</td>
              </tr>
              <tr>
                <td><strong>Mini</strong></td>
                <td><code>favicon.svg</code><br><span class="fs-faible">504&nbsp;octets</span></td>
                <td>16&nbsp;px et en dessous, onglet</td>
                <td>Le drapeau et son étoile, sans écu. La seule marque qui tienne dans
                    un carré de seize pixels.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h4 class="fs-mt-8">Tailles d'emploi</h4>
        <p class="doc-bloc-note">
          Où l'emblème se place dans une page, et avec quel format à chaque fois.
        </p>

        <div class="doc-demo" data-libelle="Tailles d'emploi">
          <div class="doc-demo-scene" style="align-items: flex-end; gap: var(--espace-8);">
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="" style="height: 120px;">
              <span class="doc-variante-nom">120 px — page d'accueil</span>
            </div>
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="" style="height: 80px;">
              <span class="doc-variante-nom">80 px — en-tête</span>
            </div>
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="" style="height: 44px;">
              <span class="doc-variante-nom">44 px — écran étroit</span>
            </div>
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="" style="height: 32px;">
              <span class="doc-variante-nom">32 px — pied de page</span>
            </div>
          </div>
        </div>

        <p class="fs-legende fs-mt-4">
          Ces quatre emplois sont montrés avec le format de référence. Les 80&nbsp;px
          de l'en-tête sont ceux du cadre fixé par le guide (§&nbsp;2.1.1)&nbsp;; les
          autres hauteurs sont propres à la charte. À 44&nbsp;px, le format moyen
          convient aussi bien, pour un tiers de poids en moins&nbsp;; à 32&nbsp;px, le
          petit format se lit mieux. Le choix relève de l'autorité qui garde les
          armoiries.
        </p>

        <h4 class="fs-mt-8">Les quatre formats côte à côte</h4>

        <div class="doc-demo fs-mt-6" data-libelle="Formats gradués">
          <div class="doc-demo-scene" style="align-items: flex-end; gap: var(--espace-8);">
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="" style="height: 96px;">
              <span class="doc-variante-nom">Grand</span>
            </div>
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries-moyen.svg') }}" alt="" style="height: 48px;">
              <span class="doc-variante-nom">Moyen</span>
            </div>
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/armoiries-ecu.svg') }}" alt="" style="height: 28px;">
              <span class="doc-variante-nom">Petit</span>
            </div>
            <div class="doc-variante">
              <img src="{{ ressource('assets/img/favicon.svg') }}" alt="" style="height: 16px;">
              <span class="doc-variante-nom">Mini</span>
            </div>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          Le petit format n'est pas un emblème redessiné. Le fichier de référence est
          une vectorisation par nappes de tons, sans objet séparable&nbsp;: y retirer des
          couches efface le dessin au lieu de le simplifier, et le redessiner
          reviendrait à modifier un symbole de l'État. La silhouette de l'écu a donc
          été relevée sur le dessin lui-même, puis employée comme découpe. Un vrai
          emblème simplifié, s'il en faut un, relève d'une décision graphique et de
          l'autorité qui garde les armoiries.
        </div>

        <div class="fs-tableau-cadre fs-mt-6">
          <table class="fs-tableau">
            <caption>Les fichiers livrés en dehors des quatre formats, et l'emploi de chacun</caption>
            <thead>
              <tr><th>Fichier</th><th>Emploi</th><th class="fs-num">Poids</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><code>armoiries.svg</code></td>
                <td>Référence. Tout usage numérique et imprimé à partir de 32&nbsp;px.</td>
                <td class="fs-num">191&nbsp;Ko</td>
              </tr>
              <tr>
                <td><code>armoiries-gris.svg</code></td>
                <td>Impression en une seule encre, photocopie, télécopie, gravure.</td>
                <td class="fs-num">191&nbsp;Ko</td>
              </tr>
              <tr>
                <td><code>armoiries-512.png</code><br><code>…-256</code>, <code>…-128</code>, <code>…-64</code></td>
                <td>Bureautique et courriel, où le vectoriel n'est pas rendu de façon
                    fiable. 512 pour une vignette de partage, 256 pour un en-tête de
                    document, 128 pour une signature, 64 pour une liste.</td>
                <td class="fs-num">180 à 8&nbsp;Ko</td>
              </tr>
              <tr>
                <td><code>favicon.svg</code></td>
                <td>Icône de l'onglet. Ce n'est pas l'emblème mais le drapeau et son
                    étoile, seule marque lisible à 16&nbsp;px.</td>
                <td class="fs-num">504&nbsp;o</td>
              </tr>
              <tr>
                <td><code>favicon-32.png</code><br><code>favicon-180.png</code></td>
                <td>Repli pour les navigateurs anciens, et carré tactile d'iOS.</td>
                <td class="fs-num">0,5 et 2,4&nbsp;Ko</td>
              </tr>
            </tbody>
          </table>
        </div>

        <dl class="fs-definitions fs-mt-6">
          <div>
            <dt><code>armoiries.svg</code></dt>
            <dd>Emblème vectoriel. Format de référence pour tout usage numérique et imprimé. 191&nbsp;Ko, 45&nbsp;Ko compressé.</dd>
          </div>
          <div>
            <dt>Taille minimale de l'emblème complet</dt>
            <dd>32&nbsp;px de hauteur. En deçà, l'écu cesse d'être identifiable et la devise devient illisible&nbsp;: on passe au petit format.</dd>
          </div>
          <div>
            <dt>Dimensions</dt>
            <dd>Rapport 468 × 550. Le fichier ne porte pas de <code>width</code> ni de <code>height</code>&nbsp;: la taille est pilotée par le CSS.</dd>
          </div>
          <div>
            <dt>Taille minimale</dt>
            <dd>Format recommandé selon la hauteur&nbsp;: grand à partir de 64&nbsp;px, moyen de 32 à 64, petit de 16 à 32, mini en dessous. Le grand et le moyen restent admis jusqu'à 32&nbsp;px, comme dans les exemples ci-dessus.</dd>
          </div>
          <div>
            <dt>Production</dt>
            <dd>Les cinq déclinaisons sont produites depuis la référence par <code>node outils/embleme.js</code>. Aucune n'est retouchée à la main.</dd>
          </div>
        </dl>

        <div class="doc-note fs-mt-6">
          Le passage en niveaux de gris ne se fait pas par conversion des luminances.
          Le rouge du drapeau donnerait <code>#707070</code> et le vert
          <code>#717171</code>, soit un seul gris pour deux couleurs, et l'écu
          perdrait son drapeau. Les trois teintes nationales reçoivent donc trois
          tons séparés, du plus sombre pour le rouge au plus clair pour l'or.
        </div>

        <div class="doc-note fs-mt-6">
          <strong>Recalage chromatique.</strong> L'écu porte le drapeau national. La
          vectorisation ayant fait dériver ses teintes, celles-ci ont été ramenées aux
          valeurs officielles fixées par cette charte&nbsp;— <code>#ef2b2d</code>,
          <code>#009e49</code> et <code>#fcd116</code>. Sans cette correction,
          l'emblème aurait juré avec les mêmes couleurs employées ailleurs sur la
          page. Les gris et les bruns de l'illustration sont conservés tels quels.
        </div>

        <div class="fs-encart fs-encart--alerte fs-mt-6">
          <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
          <div class="fs-encart-corps">
            <p class="fs-encart-titre">Statut du fichier</p>
            <p>
              Ce fichier est dérivé par vectorisation d'un rendu raster de l'emblème.
              Il convient à la conception et au développement des interfaces. Pour une
              mise en production, la version vectorielle de référence doit être obtenue
              auprès du Secrétariat général du Gouvernement et se substituer à
              celle-ci.
            </p>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Filet national <code>.fs-filet</code></h3>
        <p class="doc-bloc-note">
          Bande longue portant la <strong>composition du drapeau</strong>&nbsp;: rouge
          au-dessus, vert au-dessous, étoile d'or centrée à cheval sur les deux. Placé
          sous le bandeau d'identification officielle, le filet marque l'appartenance
          à l'État et n'est pas employé comme séparateur décoratif à l'intérieur
          d'une page.
        </p>

        <div class="doc-note">
          <strong>Une seule composition, quel que soit le format.</strong> Le repère
          court, le filet pleine largeur et le bandeau d'une carte mise en avant
          portent tous le drapeau dans le même ordre. Une marque de l'État ne se
          réarrange pas selon la place dont elle dispose.
        </div>

        <div class="doc-note fs-mt-4">
          <strong>Ce que cela demande à la hauteur.</strong> À 14&nbsp;px, les deux
          moitiés ne font que 7&nbsp;px chacune. La composition se lit de près, moins
          bien de loin. La variante <code>--epais</code>, à 20&nbsp;px, donne deux bandes
          de 10&nbsp;px et porte mieux&nbsp;; c'est celle à préférer en ouverture de page.
          La hauteur se règle par <code>--filet-hauteur</code> sans rien recalculer,
          l'étoile suivant toujours.
        </div>

        <div class="doc-demo" data-libelle="Filet national">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-filet"></div>
            <div class="fs-filet fs-filet--fin"></div>
            <div class="fs-filet fs-filet--epais"></div>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Trois hauteurs&nbsp;: <code>--fin</code> à 10&nbsp;px pour un bandeau interne,
          la hauteur courante à 14&nbsp;px, <code>--epais</code> à 20&nbsp;px lorsque le
          filet ouvre une page d'accueil. L'étoile suit la hauteur, elle n'a pas de
          réglage propre.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Repère national <code>.fs-repere</code></h3>
        <p class="doc-bloc-note">
          Vignette courte portant la <strong>composition du drapeau</strong>&nbsp;:
          rouge au-dessus, vert au-dessous, étoile centrée à cheval sur les deux, dans
          des proportions proches de celles du drapeau lui-même. Il ponctue un titre
          de section ou une ouverture de page, là où le filet pleine largeur serait
          trop appuyé. Le drapeau entier tient en deux couches de fond, sans élément
          superposé&nbsp;: le repère peut donc se composer dans un pseudo-élément, là
          où l'on ne peut pas poser de classe.
        </p>

        <div class="doc-note">
          <strong>Deux objets, une seule composition.</strong> Le repère et le filet ne
          se distinguent plus que par leur emploi. Le repère ponctue un titre, le
          filet ouvre une page. Le drapeau y figure dans le même ordre, avec la même
          étoile centrée.
        </div>

        <div class="doc-demo" data-libelle="Repère national">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <span class="fs-repere"></span>
            <span class="fs-repere" style="width: 96px; --repere-hauteur: 20px;"></span>
            <span class="fs-repere fs-repere--centre"></span>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Un seul repère par niveau de titre, toujours à la même place.</li>
              <li>Conserver les proportions du drapeau&nbsp;: deux bandes égales, étoile centrée à cheval sur les deux.</li>
              <li>Ajuster la largeur et la hauteur par les variables du composant, pas en redéfinissant le dégradé.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Remplacer l'étoile par un point, un losange ou une autre forme.</li>
              <li>Grossir l'étoile jusqu'à couvrir la hauteur&nbsp;: sur le drapeau, elle laisse respirer les deux bandes.</li>
              <li>Inverser les bandes. Le rouge est toujours au-dessus du vert.</li>
              <li>Employer le repère comme puce de liste ou séparateur répété.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Motif tissé <code>.fs-tissage</code></h3>
        <p class="doc-bloc-note">
          Inspiré des rayures du Faso Dan Fani, le tissu traditionnel. Les bandes
          sont de largeur inégale et séparées d'un fil clair, comme sur le métier à
          tisser. Le motif se distingue ainsi du filet officiel, qu'il ne remplace
          pas. Il habille les lisières (bas d'un bandeau, tête d'un pied de page,
          ouverture d'une section), et reste de faible hauteur.
        </p>

        <div class="doc-demo" data-libelle="Motif tissé">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-tissage"></div>
            <div class="fs-tissage fs-tissage--fin"></div>
            <div style="display:flex; gap: var(--espace-4); height: 96px;">
              <div class="fs-tissage fs-tissage--vertical"></div>
              <p class="fs-legende" style="align-self:center; margin:0;">Variante verticale, pour border une colonne ou une citation.</p>
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Hauteur de 4 à 8&nbsp;px, en pleine largeur ou sur une courte longueur en repère.</li>
              <li>Sur un fond coloré, adapter le fil clair via <code>--tissage-fond</code>.</li>
              <li>Un seul motif tissé par écran. Il accentue, il ne meuble pas.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Le poser en fond derrière du texte ou des composants.</li>
              <li>L'employer à la place du filet officiel sous le bandeau d'identification.</li>
              <li>L'agrandir au-delà de 12&nbsp;px&nbsp;: il devient un drapeau déformé.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Règles d'emploi de l'emblème</h3>

        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Conserver une zone de protection au moins égale à la moitié de la hauteur de l'emblème, libre de tout texte ou image.</li>
              <li>Afficher l'emblème à 80&nbsp;px de hauteur en en-tête, dans le cadre de 76&nbsp;×&nbsp;80&nbsp;px placé à 9&nbsp;px du haut de page (guide, §&nbsp;2.1.1)&nbsp;; 44&nbsp;px sur écran étroit, 32&nbsp;px en pied de page.</li>
              <li>Employer la version officielle des armoiries, avec l'encadré, depuis le fichier vectoriel (guide, §&nbsp;2.1.1).</li>
              <li>Placer le drapeau avant les armoiries lorsqu'il les accompagne.</li>
              <li>Placer l'emblème sur un fond uni, clair ou très sombre, offrant un contraste suffisant.</li>
              <li>Associer systématiquement l'emblème au nom de l'administration responsable du service.</li>
              <li>Renseigner un texte de remplacement explicite lorsque l'emblème porte une information, et un texte vide lorsqu'il est redondant.</li>
            </ul>
          </div>

          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Déformer, recadrer, faire pivoter ou recolorer l'emblème, y compris en version monochrome non validée.</li>
              <li>Appliquer une ombre portée, un contour, un dégradé ou un effet de relief.</li>
              <li>Poser l'emblème sur une photographie, une texture ou un fond chargé.</li>
              <li>Utiliser l'emblème comme motif de fond, filigrane, favicon décoratif ou élément d'illustration.</li>
              <li>Associer l'emblème à un service qui n'émane pas d'une administration publique.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Co-marquage</h3>
        <p class="doc-bloc-note">
          Un service porté conjointement par l'État et un partenaire (agence,
          établissement public, organisation internationale) fait coexister deux
          marques. L'ordre n'est pas négociable&nbsp;: l'emblème de l'État vient en
          premier, car c'est lui qui engage la responsabilité du service.
        </p>

        <div class="doc-demo" data-libelle="Co-marquage">
          <div class="doc-demo-scene">
            <div class="fs-rangee" style="gap: var(--espace-5);">
              <a class="fs-marque" href="#" style="padding-right: var(--espace-5); border-right: var(--trait-fin) solid var(--bordure);">
                <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="Armoiries du Burkina Faso">
                <span class="fs-marque-texte">
                  <span class="fs-marque-pays">Burkina Faso</span>
                  <span class="fs-marque-entite">Ministère de la Santé</span>
                </span>
              </a>
              <span style="display:grid; place-items:center; width:92px; height:44px; background: var(--surface-enfoncee); border: var(--trait-fin) dashed var(--bordure-forte); border-radius: var(--rayon-sm); font-size: var(--taille-2xs); color: var(--texte-faible); text-align:center;">
                Marque<br>partenaire
              </span>
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Placer l'emblème de l'État en premier dans le sens de lecture, séparé de la marque partenaire par un filet vertical.</li>
              <li>Limiter la marque partenaire à la hauteur de l'emblème, pas au-delà.</li>
              <li>Conserver la zone de protection de l'emblème&nbsp;: la marque partenaire n'empiète pas dessus.</li>
              <li>N'admettre qu'un seul partenaire dans le bloc-marque&nbsp;; les autres figurent en pied de page.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Placer une marque partenaire avant l'emblème ou au-dessus de lui.</li>
              <li>Composer un logotype mixte associant l'emblème à un autre signe.</li>
              <li>Faire figurer une marque commerciale dans le bloc-marque, quel que soit le financement du service.</li>
              <li>Aligner les deux marques sans séparateur&nbsp;: elles paraîtraient émaner d'une même entité.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Devise nationale</h3>
        <p class="doc-bloc-note">
          La devise figure dans le bloc-marque en romain, sans italique (guide,
          §&nbsp;2.4, point&nbsp;2), au palier typographique le plus discret. Elle n'est ni un slogan
          promotionnel, ni un titre&nbsp;: elle ne doit pas être mise en avant au
          détriment de l'intitulé du service. Sur écran étroit, elle est masquée pour
          préserver la lisibilité de la dénomination.
        </p>
        <div class="doc-note">
          La devise portée par l'emblème en vigueur est
          <strong>«&nbsp;La Patrie ou la Mort, nous Vaincrons&nbsp;»</strong>. Toute reprise
          doit être conforme au fichier officiel de référence, y compris dans la
          ponctuation et les capitales. C'est une citation du texte constitutionnel
          et non un libellé&nbsp;: la règle de la seule majuscule initiale (guide,
          §&nbsp;2.4, point&nbsp;4) ne s'y applique pas. Le guide, rédigé en 2024, porte
          encore l'ancienne devise «&nbsp;Unité-Progrès-Justice&nbsp;», remplacée par la
          loi constitutionnelle n°&nbsp;033-2024/ALT du 29&nbsp;octobre 2024.
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="couleurs">
      <h2>Couleurs</h2>
      <p class="doc-section-intro">
        Les trois couleurs du drapeau adopté le 4 août 1984 constituent la souche
        chromatique du système, aux valeurs fixées par le guide de la charte graphique
        des plateformes numériques de l'administration publique (MTDPCE, 2024)&nbsp;:
        rouge <code>#EF2B2D</code> (RVB 239/43/45), jaune <code>#FCD116</code> (RVB
        252/209/22), vert <code>#009E49</code> (RVB 0/158/73). Elles sont trop saturées
        pour habiller une interface entière&nbsp;: chacune est déclinée en dix paliers, la
        teinte officielle restant fixée au palier indiqué. Les neutres sont des gris
        pâles légèrement réchauffés, que le guide recommande pour les fonds.
      </p>

      <div class="doc-trio">
        <div class="doc-trio-tuile doc-trio-tuile--rouge" data-copier="#ef2b2d" title="Copier la valeur">
          <span class="doc-trio-nom">Rouge</span>
          <span class="doc-trio-sens">Révolution, courage, sang versé pour la patrie</span>
          <span class="doc-trio-hex">#ef2b2d · Pantone 032 C</span>
        </div>
        <div class="doc-trio-tuile doc-trio-tuile--or" data-copier="#fcd116" title="Copier la valeur">
          <span class="doc-trio-nom">Or</span>
          <span class="doc-trio-sens">Lumière de l'étoile, richesse du sous-sol</span>
          <span class="doc-trio-hex">#fcd116 · Pantone 116 C</span>
        </div>
        <div class="doc-trio-tuile doc-trio-tuile--vert" data-copier="#009e49" title="Copier la valeur">
          <span class="doc-trio-nom">Vert</span>
          <span class="doc-trio-sens">Espérance, abondance des richesses agricoles</span>
          <span class="doc-trio-hex">#009e49 · Pantone 355 C</span>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Rouge&nbsp;— révolution et courage</h3>
        <p class="doc-bloc-note">
          Employé pour le registre grave&nbsp;: erreurs, suppressions, alertes critiques,
          signalement de fraude, conformément à la convention «&nbsp;rouge&nbsp;: alerte,
          danger, interruption&nbsp;» (guide, §&nbsp;2.2, point&nbsp;5). Comme pour le vert,
          les boutons et les messages emploient le palier 600. Sa charge symbolique interdit de l'utiliser comme
          couleur d'accentuation ordinaire ou de mise en avant commerciale.
        </p>
        <div class="doc-echelle">
          <div class="doc-ton doc-ton--clair"  style="background:#fff1f1" data-copier="#fff1f1"><span class="doc-ton-palier">50</span><span class="doc-ton-hex">#fff1f1</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#ffdfdf" data-copier="#ffdfdf"><span class="doc-ton-palier">100</span><span class="doc-ton-hex">#ffdfdf</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#ffc4c4" data-copier="#ffc4c4"><span class="doc-ton-palier">200</span><span class="doc-ton-hex">#ffc4c4</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#fd9a9a" data-copier="#fd9a9a"><span class="doc-ton-palier">300</span><span class="doc-ton-hex">#fd9a9a</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#f76565" data-copier="#f76565"><span class="doc-ton-palier">400</span><span class="doc-ton-hex">#f76565</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#ef2b2d" data-copier="#ef2b2d"><span class="doc-ton-drapeau">Drapeau</span><span class="doc-ton-palier">500</span><span class="doc-ton-hex">#ef2b2d</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#d41f1f" data-copier="#d41f1f"><span class="doc-ton-palier">600</span><span class="doc-ton-hex">#d41f1f</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#b01616" data-copier="#b01616"><span class="doc-ton-palier">700</span><span class="doc-ton-hex">#b01616</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#8a1114" data-copier="#8a1114"><span class="doc-ton-palier">800</span><span class="doc-ton-hex">#8a1114</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#600d10" data-copier="#600d10"><span class="doc-ton-palier">900</span><span class="doc-ton-hex">#600d10</span></div>
        </div>
        <p class="fs-legende">
          Équivalence d'impression usuelle&nbsp;: Pantone 032 C.
          Texte blanc sur le palier 500&nbsp;: <span data-contraste="#ef2b2d/#ffffff"></span>, insuffisant pour
          du texte courant&nbsp;; sur le palier 600, employé par les boutons et les messages&nbsp;:
          <span data-contraste="#d41f1f/#ffffff"></span>
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Vert&nbsp;— espérance et richesses agricoles</h3>
        <p class="doc-bloc-note">
          Couleur de l'action, et la plus présente dans une interface conforme.
          C'est le <strong>palier 600</strong> (<code>#007d3c</code>) qui porte les
          boutons principaux, les liens, les états de validation et les repères de
          navigation active&nbsp;: la teinte officielle du palier 500 n'offre pas un
          contraste suffisant avec du texte blanc. Elle reste celle des marques
          nationales (filet, repère, emblème) et des aplats qui ne portent pas de
          texte.
        </p>
        <div class="doc-echelle">
          <div class="doc-ton doc-ton--clair"  style="background:#e9f8ef" data-copier="#e9f8ef"><span class="doc-ton-palier">50</span><span class="doc-ton-hex">#e9f8ef</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#cdeed9" data-copier="#cdeed9"><span class="doc-ton-palier">100</span><span class="doc-ton-hex">#cdeed9</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#9fdcb7" data-copier="#9fdcb7"><span class="doc-ton-palier">200</span><span class="doc-ton-hex">#9fdcb7</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#66c38e" data-copier="#66c38e"><span class="doc-ton-palier">300</span><span class="doc-ton-hex">#66c38e</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#2fae68" data-copier="#2fae68"><span class="doc-ton-palier">400</span><span class="doc-ton-hex">#2fae68</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#009e49" data-copier="#009e49"><span class="doc-ton-drapeau">Drapeau</span><span class="doc-ton-palier">500</span><span class="doc-ton-hex">#009e49</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#007d3c" data-copier="#007d3c"><span class="doc-ton-palier">600</span><span class="doc-ton-hex">#007d3c</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#045527" data-copier="#045527"><span class="doc-ton-palier">700</span><span class="doc-ton-hex">#045527</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#04411f" data-copier="#04411f"><span class="doc-ton-palier">800</span><span class="doc-ton-hex">#04411f</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#032e16" data-copier="#032e16"><span class="doc-ton-palier">900</span><span class="doc-ton-hex">#032e16</span></div>
        </div>
        <p class="fs-legende">
          Équivalence d'impression usuelle&nbsp;: Pantone 355 C.
          Texte blanc sur le palier 500&nbsp;: <span data-contraste="#009e49/#ffffff"></span>, insuffisant pour
          du texte courant&nbsp;; sur le palier 600, employé par les boutons et les liens&nbsp;:
          <span data-contraste="#007d3c/#ffffff"></span>
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Or&nbsp;— lumière de l'étoile</h3>
        <p class="doc-bloc-note">
          Couleur de mise en avant, employée avec parcimonie. Le jaune pur étant très
          clair, il ne porte pas de texte blanc. Le palier 300 s'accompagne
          toujours d'un texte sombre. Les paliers 600 et au-delà tirent vers l'ocre
          et servent à écrire sur fond clair.
        </p>
        <p class="doc-bloc-note">
          Le guide déconseille d'associer le jaune et le vert (§&nbsp;2.2, point&nbsp;2).
          La règle vaut pour l'interface&nbsp;: un aplat d'or ne se pose pas contre un
          bouton vert, une série jaune ne côtoie pas une série verte. Elle ne vaut pas
          pour les marques nationales&nbsp;— emblème, filet, repère, favicon&nbsp;— où le
          drapeau associe par nature le jaune et le vert.
        </p>
        <div class="doc-echelle">
          <div class="doc-ton doc-ton--clair"  style="background:#fffbe6" data-copier="#fffbe6"><span class="doc-ton-palier">50</span><span class="doc-ton-hex">#fffbe6</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#fff5c2" data-copier="#fff5c2"><span class="doc-ton-palier">100</span><span class="doc-ton-hex">#fff5c2</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#ffe98a" data-copier="#ffe98a"><span class="doc-ton-palier">200</span><span class="doc-ton-hex">#ffe98a</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#fcd116" data-copier="#fcd116"><span class="doc-ton-drapeau">Drapeau</span><span class="doc-ton-palier">300</span><span class="doc-ton-hex">#fcd116</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#e6bb00" data-copier="#e6bb00"><span class="doc-ton-palier">400</span><span class="doc-ton-hex">#e6bb00</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#c9a200" data-copier="#c9a200"><span class="doc-ton-palier">500</span><span class="doc-ton-hex">#c9a200</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#a38300" data-copier="#a38300"><span class="doc-ton-palier">600</span><span class="doc-ton-hex">#a38300</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#7d6400" data-copier="#7d6400"><span class="doc-ton-palier">700</span><span class="doc-ton-hex">#7d6400</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#5c4900" data-copier="#5c4900"><span class="doc-ton-palier">800</span><span class="doc-ton-hex">#5c4900</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#3d3100" data-copier="#3d3100"><span class="doc-ton-palier">900</span><span class="doc-ton-hex">#3d3100</span></div>
        </div>
        <p class="fs-legende">
          Équivalence d'impression usuelle&nbsp;: Pantone 116 C.
          Texte noir sur le palier 300&nbsp;: <span data-contraste="#fcd116/#211d1a"></span> ·
          Texte blanc sur le palier 300&nbsp;: <span data-contraste="#fcd116/#ffffff"></span>
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Neutres</h3>
        <p class="doc-bloc-note">
          Fonds, bordures et textes. La base est volontairement chaude. Un gris
          bleuté introduirait une teinte étrangère à la souche nationale et ferait
          jurer les aplats verts et or.
        </p>
        <div class="doc-echelle doc-echelle--large">
          <div class="doc-ton doc-ton--clair"  style="background:#ffffff; box-shadow: inset 0 0 0 1px #e2ddd6" data-copier="#ffffff"><span class="doc-ton-palier">0</span><span class="doc-ton-hex">#ffffff</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#fcfbf9" data-copier="#fcfbf9"><span class="doc-ton-palier">25</span><span class="doc-ton-hex">#fcfbf9</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#f7f5f2" data-copier="#f7f5f2"><span class="doc-ton-palier">50</span><span class="doc-ton-hex">#f7f5f2</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#efece7" data-copier="#efece7"><span class="doc-ton-palier">100</span><span class="doc-ton-hex">#efece7</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#e2ddd6" data-copier="#e2ddd6"><span class="doc-ton-palier">200</span><span class="doc-ton-hex">#e2ddd6</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#cfc8bf" data-copier="#cfc8bf"><span class="doc-ton-palier">300</span><span class="doc-ton-hex">#cfc8bf</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#a9a199" data-copier="#a9a199"><span class="doc-ton-palier">400</span><span class="doc-ton-hex">#a9a199</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#8e867f" data-copier="#8e867f"><span class="doc-ton-palier">450</span><span class="doc-ton-hex">#8e867f</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#837b73" data-copier="#837b73"><span class="doc-ton-palier">500</span><span class="doc-ton-hex">#837b73</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#756d66" data-copier="#756d66"><span class="doc-ton-palier">550</span><span class="doc-ton-hex">#756d66</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#635c55" data-copier="#635c55"><span class="doc-ton-palier">600</span><span class="doc-ton-hex">#635c55</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#4a443e" data-copier="#4a443e"><span class="doc-ton-palier">700</span><span class="doc-ton-hex">#4a443e</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#332e2a" data-copier="#332e2a"><span class="doc-ton-palier">800</span><span class="doc-ton-hex">#332e2a</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#211d1a" data-copier="#211d1a"><span class="doc-ton-palier">900</span><span class="doc-ton-hex">#211d1a</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#14110f" data-copier="#14110f"><span class="doc-ton-palier">950</span><span class="doc-ton-hex">#14110f</span></div>
        </div>
        <p class="fs-legende">
          Texte courant sur fond blanc&nbsp;: <span data-contraste="#211d1a/#ffffff"></span> ·
          Texte atténué sur fond blanc&nbsp;: <span data-contraste="#635c55/#ffffff"></span>
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Couleurs de support</h3>
        <p class="doc-bloc-note">
          Deux familles complètent la souche nationale, cantonnées à un registre
          fonctionnel&nbsp;: le bleu administratif pour l'information neutre
          (<code>--info</code>), l'ocre pour l'avertissement (<code>--alerte</code>).
          Le bleu administratif ne structure ni un en-tête, ni une navigation, ni une
          action. Il ne se confond pas avec le bleu nuit des palettes d'espace,
          présenté plus bas, qui vient du guide et porte, lui, le bandeau officiel et
          la navigation de l'espace public.
        </p>
        <div class="doc-echelle doc-echelle--libre">
          <div class="doc-ton doc-ton--clair"  style="background:#eef4f9" data-copier="#eef4f9"><span class="doc-ton-palier">Bleu 50</span><span class="doc-ton-hex">#eef4f9</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#d6e5f0" data-copier="#d6e5f0"><span class="doc-ton-palier">Bleu 100</span><span class="doc-ton-hex">#d6e5f0</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#2a6d9b" data-copier="#2a6d9b"><span class="doc-ton-palier">Bleu 500</span><span class="doc-ton-hex">#2a6d9b</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#173f5f" data-copier="#173f5f"><span class="doc-ton-palier">Bleu 700</span><span class="doc-ton-hex">#173f5f</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#fdf4e3" data-copier="#fdf4e3"><span class="doc-ton-palier">Ocre 50</span><span class="doc-ton-hex">#fdf4e3</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#b07800" data-copier="#b07800"><span class="doc-ton-palier">Ocre 500</span><span class="doc-ton-hex">#b07800</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#7d5500" data-copier="#7d5500"><span class="doc-ton-palier">Ocre 700</span><span class="doc-ton-hex">#7d5500</span></div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Palettes d'espace <code>.fs-espace-public</code> <code>.fs-espace-prive</code></h3>
        <p class="doc-bloc-note">
          Le guide demande de réserver à chaque espace d'une plateforme sa couleur
          (§&nbsp;2.2, point&nbsp;8)&nbsp;: l'usager sait d'un coup d'œil s'il consulte
          l'espace public ou son espace personnel. Les deux palettes que le guide
          donne en exemple sont reprises telles quelles, en jetons
          <code>--public-*</code> et <code>--prive-*</code>. La classe d'espace se pose
          sur <code>&lt;body&gt;</code>.
        </p>

        <h4 class="fs-mt-6">Espace public</h4>
        <div class="doc-echelle doc-echelle--libre">
          <div class="doc-ton doc-ton--sombre" style="background:#00213b" data-copier="#00213b"><span class="doc-ton-palier">Public 900</span><span class="doc-ton-hex">#00213b</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#1b384f" data-copier="#1b384f"><span class="doc-ton-palier">Public 700</span><span class="doc-ton-hex">#1b384f</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#2c224e" data-copier="#2c224e"><span class="doc-ton-palier">Violet</span><span class="doc-ton-hex">#2c224e</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#eaecef" data-copier="#eaecef"><span class="doc-ton-palier">Fond</span><span class="doc-ton-hex">#eaecef</span></div>
        </div>
        <p class="fs-legende">
          Jetons <code>--public-900</code>, <code>--public-700</code>,
          <code>--public-violet</code>, <code>--public-fond</code>&nbsp;; RVB 0/33/59,
          27/56/79, 44/34/78, 234/236/239. Avec
          <code>.fs-espace-public</code>, la barre de navigation passe en bleu nuit
          (<code>--public-900</code>), texte blanc, page active sur
          <code>--public-700</code>. Texte blanc sur <code>--public-900</code>&nbsp;:
          <span data-contraste="#00213b/#ffffff"></span>
        </p>

        <h4 class="fs-mt-6">Espace privé</h4>
        <div class="doc-echelle doc-echelle--libre">
          <div class="doc-ton doc-ton--clair"  style="background:#78cc99" data-copier="#78cc99"><span class="doc-ton-palier">Vert</span><span class="doc-ton-hex">#78cc99</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#f2aa3e" data-copier="#f2aa3e"><span class="doc-ton-palier">Orange</span><span class="doc-ton-hex">#f2aa3e</span></div>
          <div class="doc-ton doc-ton--sombre" style="background:#535fdd" data-copier="#535fdd"><span class="doc-ton-palier">Bleu</span><span class="doc-ton-hex">#535fdd</span></div>
          <div class="doc-ton doc-ton--clair"  style="background:#f0f0f7" data-copier="#f0f0f7"><span class="doc-ton-palier">Fond</span><span class="doc-ton-hex">#f0f0f7</span></div>
        </div>
        <p class="fs-legende">
          Jetons <code>--prive-vert</code>, <code>--prive-orange</code>,
          <code>--prive-bleu</code>, <code>--prive-fond</code>&nbsp;; RVB 120/204/153,
          242/170/62, 83/95/221, 240/240/247. Avec
          <code>.fs-espace-prive</code>, la barre de navigation prend le fond
          gris-lavande (<code>--prive-fond</code>), texte sombre, page active
          soulignée de <code>--prive-bleu</code>. Texte courant sur
          <code>--prive-fond</code>&nbsp;: <span data-contraste="#f0f0f7/#211d1a"></span>
        </p>

        <div class="doc-note fs-mt-4">
          Les autres teintes de chaque palette sont livrées comme jetons, pour les
          éléments propres à l'espace. Elles ne remplacent pas les rôles
          sémantiques&nbsp;: un bouton reste vert, une erreur reste rouge, dans un
          espace comme dans l'autre (guide, §&nbsp;2.2, point&nbsp;4). Le vert et
          l'orange de l'espace privé sont des teintes claires&nbsp;: ils ne servent pas
          à écrire et ne portent pas de texte blanc (§&nbsp;2.2, point&nbsp;2, qui
          écarte les couleurs pâles pour l'information à lire).
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Règles d'emploi de la couleur</h3>
        <p class="doc-bloc-note">
          La couleur code l'information&nbsp;: elle différencie et identifie ce qui est
          affiché. Les règles suivantes reprennent le guide (§&nbsp;2.2), avec la
          manière dont la charte les applique.
        </p>

        <ol class="fs-liste-puces fs-mesure">
          <li>
            <strong>Jamais seule.</strong> La couleur n'est pas le seul moyen de
            communiquer une information ni de distinguer deux objets&nbsp;: un libellé,
            une forme ou une position la double toujours, pour les usagers
            daltoniens ou achromates (point&nbsp;1). Dans la charte, un état n'est
            jamais signalé par sa seule couleur&nbsp;: il porte un libellé.
          </li>
          <li>
            <strong>Associations à éviter.</strong> Rouge avec bleu, jaune avec
            violet, jaune avec vert (point&nbsp;2). Les séries de graphiques
            (<code>--serie-1</code> à <code>--serie-6</code>) n'emploient donc ni
            jaune ni bleu&nbsp;: des verts, des rouges et des neutres seulement.
            Exception&nbsp;: les marques nationales, où le drapeau associe par nature
            le jaune et le vert.
          </li>
          <li>
            <strong>Fonds.</strong> Pas de fond de fenêtre rouge, jaune ou dérivé
            (vert, orange)&nbsp;: gris pâle de préférence, sinon bleu foncé
            (point&nbsp;2). C'est pourquoi les surfaces sont des neutres et le bandeau
            officiel <code>.fs-officiel</code> est en bleu nuit
            (<code>--public-900</code>). Pas de bleu pâle, ni de couleur pâle en
            général, pour un texte que l'usager doit lire.
          </li>
          <li>
            <strong>Affichage et saisie.</strong> Les zones d'affichage ont un fond
            différent de celui des zones de saisie, de préférence gris (point&nbsp;3)&nbsp;:
            un champ en lecture seule prend le fond <code>--surface-enfoncee</code>.
          </li>
          <li>
            <strong>Mêmes codages partout.</strong> Une couleur garde le même sens
            dans une application et dans toutes celles qui servent les mêmes usagers
            (point&nbsp;4). C'est l'objet des rôles sémantiques présentés plus bas.
          </li>
          <li>
            <strong>Conventions.</strong> Vert&nbsp;: tout se passe correctement.
            Jaune ou orange&nbsp;: attention, vigilance. Rouge&nbsp;: alerte, danger,
            interruption (point&nbsp;5). Les rôles <code>--succes</code> (vert),
            <code>--alerte</code> (ocre) et <code>--danger</code> (rouge) suivent cette
            répartition.
          </li>
          <li>
            <strong>Cinq couleurs au plus</strong> dans un même écran, et dans une
            même application&nbsp;; au-delà, l'usager ne saisit plus le rôle de chacune
            (point&nbsp;6). Un graphique n'affiche donc pas plus de cinq séries à la
            fois.
          </li>
          <li>
            <strong>Impression en noir et blanc.</strong> Chaque couleur doit rester
            distincte en niveaux de gris sur une imprimante monochrome&nbsp;; si les
            niveaux de gris ne suffisent pas, prévoir des hachures (point&nbsp;7).
            L'emblème a sa propre version en gris, <code>armoiries-gris.svg</code>.
          </li>
          <li>
            <strong>Une couleur par espace</strong>, selon les palettes d'espace
            ci-dessus (point&nbsp;8).
          </li>
        </ol>

        <h4 class="fs-mt-8">Couleur des textes</h4>
        <p class="doc-bloc-note">
          Le texte est noir par défaut, nuancé vers le gris pour les textes généraux
          (guide, §&nbsp;2.3). Le guide obtient cette nuance par une opacité&nbsp;; la
          charte emploie des gris pleins de l'échelle des neutres&nbsp;—
          <code>--texte</code> (<code>#211d1a</code>) pour le texte courant,
          <code>--texte-attenue</code> (<code>#635c55</code>) pour le texte
          secondaire&nbsp;— dont le contraste se calcule sans dépendre du fond.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Rôles sémantiques</h3>
        <p class="doc-bloc-note">
          Les composants ne référencent pas un palier directement. Ils consomment
          un rôle, qui pointe vers un palier. C'est cette indirection qui rend le
          thème sombre possible sans toucher à une seule règle de composant.
        </p>

        <div class="doc-roles">
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--action)"></span>
            <div><span class="doc-role-nom">--action</span><span class="doc-role-usage">Boutons principaux, liens, navigation active, validation</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--accent)"></span>
            <div><span class="doc-role-nom">--accent</span><span class="doc-role-usage">Suppression, alerte critique, urgence nationale</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--relief)"></span>
            <div><span class="doc-role-nom">--relief</span><span class="doc-role-usage">Mise en avant ponctuelle, repère, surlignage</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--succes)"></span>
            <div><span class="doc-role-nom">--succes</span><span class="doc-role-usage">Dossier validé, opération aboutie</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--alerte)"></span>
            <div><span class="doc-role-nom">--alerte</span><span class="doc-role-usage">Complément demandé, délai proche, mise en garde</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--danger)"></span>
            <div><span class="doc-role-nom">--danger</span><span class="doc-role-usage">Erreur de saisie, rejet, action irréversible</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--info)"></span>
            <div><span class="doc-role-nom">--info</span><span class="doc-role-usage">Information neutre, repère documentaire</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--surface-enfoncee); border-color: var(--bordure)"></span>
            <div><span class="doc-role-nom">--surface-enfoncee</span><span class="doc-role-usage">Fonds de tableau, zones inactives, en-têtes</span></div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Répartition chromatique</h3>
        <p class="doc-bloc-note">
          Une interface conforme n'est pas une interface tricolore. Les trois couleurs
          nationales sont des signaux, et leur force tient à leur rareté. Proportion
          de référence&nbsp;:
        </p>

        <div class="doc-demo" data-libelle="Proportions indicatives">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div style="display:flex; height:56px; border-radius: var(--rayon-md); overflow:hidden; font-size: var(--taille-2xs); font-weight:700; color:#fff;">
              <div style="flex: 78; background: var(--neutre-100); color: var(--neutre-700); display:grid; place-items:center;">Neutres&nbsp;— 78&nbsp;%</div>
              <div style="flex: 14; background: var(--vert-500); display:grid; place-items:center;">Vert&nbsp;— 14&nbsp;%</div>
              <div style="flex: 5; background: var(--or-300); color: var(--neutre-900); display:grid; place-items:center;">Or</div>
              <div style="flex: 3; background: var(--rouge-500); display:grid; place-items:center;">Rouge</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="typographie">
      <h2>Typographie</h2>
      <p class="doc-section-intro">
        Deux familles, celles que propose le guide (§&nbsp;2.4, point&nbsp;5), chacune
        avec un rôle assigné. Montserrat porte les titres principaux et les
        boutons d'action (§&nbsp;2.4.1.1). Roboto porte tout le reste&nbsp;: texte
        courant, libellés de champ, liens de navigation et menus, onglets, en-têtes
        de tableau (§&nbsp;2.4.1.2). Les niveaux de lecture se font ressortir par le
        corps et la graisse, pas par une troisième police.
      </p>

      <div class="doc-bloc">
        <h3>Les deux familles</h3>
        <p class="doc-bloc-note">
          Pas une de plus&nbsp;: chaque famille supplémentaire est un fichier de plus
          sur le chemin critique, pour des services conçus d'abord pour un réseau
          instable.
        </p>

        <div class="doc-demo" data-libelle="Spécimen">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div>
              <p class="fs-legende">Montserrat&nbsp;— titres principaux, boutons d'action</p>
              <p style="font-family: var(--police-titre); font-size: 2rem; font-weight: 800; letter-spacing: -0.02em; margin: var(--espace-2) 0;">
                Burkina Faso
              </p>
              <p style="font-family: var(--police-titre); font-size: 1.125rem; line-height: 1.6; margin: 0;">
                ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
                abcdefghijklmnopqrstuvwxyz<br>
                0123456789 &amp; ’ «&nbsp;» — … €
              </p>
            </div>

            <hr>

            <div>
              <p class="fs-legende">Roboto&nbsp;— texte courant, libellés, navigation, onglets, tableaux</p>
              <p style="font-family: var(--police-texte); font-size: 1.0625rem; line-height: 1.7; margin: var(--espace-2) 0 0; max-width: 54ch;">
                Le récépissé de dépôt est délivré immédiatement après validation du
                formulaire. Il ne vaut pas acte et ne peut être produit comme pièce
                justificative.
              </p>
              <p style="font-family: var(--police-texte); font-size: 1.125rem; line-height: 1.6; margin-top: var(--espace-4);">
                ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
                abcdefghijklmnopqrstuvwxyz<br>
                0123456789 &amp; ’ «&nbsp;» — … €
              </p>
            </div>

            <hr>

            <div>
              <p class="fs-legende">Accents et ligatures du français</p>
              <p style="font-family: var(--police-texte); font-size: 1.375rem; line-height: 1.7; margin: var(--espace-2) 0 0;">
                À Â Ä Ç É È Ê Ë Î Ï Ô Ö Ù Û Ü Ÿ Œ Æ<br>
                à â ä ç é è ê ë î ï ô ö ù û ü ÿ œ æ
              </p>
              <p class="fs-legende fs-mt-4">
                Le sous-ensemble servi couvre l'intégralité du français, <strong>Œ et œ
                compris</strong>, sans eux, « cœur » et « œuvre » basculeraient dans
                la police de repli au milieu d'un mot.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Graisses</h3>
        <p class="doc-bloc-note">
          Les fichiers couvrent toutes les graisses de 100 à 900, mais la charte
          n'en emploie que cinq, chacune avec un emploi assigné. Une graisse employée
          hors de son rôle affaiblit la hiérarchie au lieu de la renforcer.
        </p>

        <div class="doc-demo" data-libelle="Graisses">
          <div class="doc-demo-scene doc-demo-scene--pile" style="display:block;">
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>400 — normale</b>Roboto, texte courant</div>
              <div class="doc-specimen-rendu" style="font-weight: 400; font-size: 1.125rem;">Demander un acte de naissance</div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>500 — moyenne</b>Roboto, libellé de champ, lien de menu</div>
              <div class="doc-specimen-rendu" style="font-weight: 500; font-size: 1.125rem;">Demander un acte de naissance</div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>600 — demi-grasse</b>Montserrat, boutons, titres de rang 4 à 6</div>
              <div class="doc-specimen-rendu" style="font-family: var(--police-titre); font-weight: 600; font-size: 1.125rem;">Demander un acte de naissance</div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>700 — grasse</b>Montserrat, titres de rang 2 et 3</div>
              <div class="doc-specimen-rendu" style="font-family: var(--police-titre); font-weight: 700; font-size: 1.125rem;">Demander un acte de naissance</div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>800 — noire</b>Montserrat, titre de rang 1, chiffres clés</div>
              <div class="doc-specimen-rendu" style="font-family: var(--police-titre); font-weight: 800; font-size: 1.125rem;">Demander un acte de naissance</div>
            </div>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          Les fichiers sont des <strong>polices variables</strong>&nbsp;: un seul fichier
          par famille couvre toutes les graisses, de 100 à 900. Cinq fichiers
          statiques auraient pesé bien davantage. Aucune graisse intermédiaire n'est
          pour autant autorisée&nbsp;— l'échelle reste celle des cinq paliers
          ci-dessus.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Chasse fixe</h3>
        <p class="doc-bloc-note">
          Références de dossier, montants dans un tableau, valeurs techniques&nbsp;: tout
          ce qui doit s'aligner en colonne ou se lire caractère par caractère.
          <strong>Aucune police n'est téléchargée pour cela</strong>&nbsp;: une chasse fixe
          ne porte aucune identité, celle du système d'exploitation convient, et
          c'est un fichier de moins sur le chemin critique.
        </p>
        <p class="doc-bloc-note">
          Ce n'est pas une troisième famille au sens du guide&nbsp;: celui-ci admet un
          codage particulier, dont une police différente, pour distinguer certains
          textes (§&nbsp;2.4, point&nbsp;3). La chasse fixe est réservée à ce codage,
          un champ ou une cellule entière à la fois, jamais au milieu d'un texte
          courant.
        </p>

        <div class="doc-demo" data-libelle="Chasse fixe">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <p style="margin:0;"><code>BF-2026-004871-OUA</code></p>
            <p class="fs-legende" style="margin:0;">
              Les chiffres du texte courant sont eux aussi tabulaires dans les
              tableaux et les montants, via <code>font-variant-numeric</code>&nbsp;: sans
              cela, une colonne de sommes ne s'aligne pas.
            </p>
            <div class="fs-tableau-cadre" style="max-width: 280px;">
              <table class="fs-tableau fs-tableau--compact">
                <tbody>
                  <tr><td>Droit de timbre</td><td class="fs-num">500&nbsp;F</td></tr>
                  <tr><td>Copies</td><td class="fs-num">1&nbsp;400&nbsp;F</td></tr>
                  <tr><td>Envoi</td><td class="fs-num">11&nbsp;500&nbsp;F</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Chargement et repli</h3>
        <p class="doc-bloc-note">
          Les polices sont servies <strong>depuis le domaine du service</strong>,
          jamais depuis un tiers. Une dépendance externe ajoute une résolution de nom,
          une connexion, et place la disponibilité d'un service public hors du contrôle
          de l'administration.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Poids réel servi, sous-ensemble latin</caption>
            <thead>
              <tr><th>Fichier</th><th>Format</th><th>Graisses</th><th class="fs-num">Poids</th></tr>
            </thead>
            <tbody>
              <tr><td><code>montserrat.woff2</code></td><td>Variable</td><td>100 à 900</td><td class="fs-num">37&nbsp;Ko</td></tr>
              <tr><td><code>roboto.woff2</code></td><td>Variable</td><td>100 à 900</td><td class="fs-num">42&nbsp;Ko</td></tr>
              <tr><td>Chasse fixe</td><td>Système</td><td>—</td><td class="fs-num">0&nbsp;Ko</td></tr>
              <tr><td><strong>Total</strong></td><td></td><td></td><td class="fs-num"><strong>79&nbsp;Ko</strong></td></tr>
            </tbody>
          </table>
        </div>

        <p class="fs-legende fs-mt-4">
          Plafond fixé par le <a href="{{ page('accessibilite') }}#budget">budget de
          performance</a>&nbsp;: 100&nbsp;Ko. Objectif&nbsp;: 60&nbsp;Ko. Le système est sous le
          plafond, au-dessus de l'objectif&nbsp;; la marge restante viendrait d'un
          sous-ensemble plus étroit que le latin complet.
        </p>

        <h4 class="fs-mt-8">Langues nationales et couverture des glyphes</h4>
        <p class="doc-bloc-note">
          Les deux fichiers sont réduits au sous-ensemble latin&nbsp;: c'est ce qui les
          fait tenir en 79&nbsp;Ko. Ce sous-ensemble couvre le français, langue
          officielle, mais <strong>pas les caractères propres aux orthographes des
          langues nationales</strong>. Tout service qui voudrait s'adresser aux
          usagers dans leur langue est directement concerné.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Caractères hors du sous-ensemble servi</caption>
            <thead>
              <tr><th>Caractère</th><th>Point de code</th><th>Employé notamment en</th><th>État</th></tr>
            </thead>
            <tbody>
              <tr>
                <td class="fs-tabulaire">ɛ Ɛ</td>
                <td class="fs-tabulaire">U+025B, U+0190</td>
                <td>Mooré, dioula</td>
                <td><span class="fs-badge fs-badge--alerte">Police de repli</span></td>
              </tr>
              <tr>
                <td class="fs-tabulaire">ɔ Ɔ</td>
                <td class="fs-tabulaire">U+0254, U+0186</td>
                <td>Dioula, bissa</td>
                <td><span class="fs-badge fs-badge--alerte">Police de repli</span></td>
              </tr>
              <tr>
                <td class="fs-tabulaire">ŋ Ŋ</td>
                <td class="fs-tabulaire">U+014B, U+014A</td>
                <td>Fulfuldé</td>
                <td><span class="fs-badge fs-badge--alerte">Police de repli</span></td>
              </tr>
              <tr>
                <td class="fs-tabulaire">ɩ ʋ</td>
                <td class="fs-tabulaire">U+0269, U+028B</td>
                <td>Mooré</td>
                <td><span class="fs-badge fs-badge--alerte">Police de repli</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-note fs-mt-6">
          <strong>Ce que cela produit à l'écran.</strong> Le navigateur remplace
          caractère par caractère&nbsp;: le mot reste lisible, mais le glyphe manquant
          est emprunté à une autre police, avec une chasse et un dessin différents.
          Sur un mot isolé cela passe, sur un paragraphe entier cela se voit.
        </div>

        <div class="doc-note fs-mt-4">
          <strong>Ce qu'il faut faire pour y remédier.</strong> Reproduire le
          sous-ensemble depuis les fichiers d'origine de Montserrat et de Roboto, tous
          deux sous licence SIL OFL, en y ajoutant les plages
          <code>U+014A-014B</code>, <code>U+0186</code>, <code>U+0190</code>,
          <code>U+0254</code>, <code>U+025B</code>, <code>U+0269</code>,
          <code>U+028B</code>, puis étendre <code>unicode-range</code> en conséquence.
          Le surcoût attendu se compte en quelques kilo-octets. Tant que ce travail
          n'est pas fait, la charte ne prétend couvrir que le français.
        </div>

        <h4 class="fs-mt-8">Repli système</h4>
        <p class="doc-bloc-note">
          Si une police ne charge pas, le texte reste lisible dans la pile de
          substitution. <code>font-display: swap</code> affiche immédiatement le
          contenu en police de repli, puis bascule&nbsp;: sur réseau lent, une page lisible
          vaut mieux qu'une page blanche.
        </p>

        <div class="doc-demo" data-libelle="Repli">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div>
              <p class="fs-legende">Titre — police attendue</p>
              <p style="font-family: var(--police-titre); font-size: 1.5rem; font-weight: 700; margin: var(--espace-1) 0 0;">Suivi de votre dossier</p>
            </div>
            <div>
              <p class="fs-legende">Titre — repli système</p>
              <p style="font-family: 'Segoe UI', system-ui, sans-serif; font-size: 1.5rem; font-weight: 700; margin: var(--espace-1) 0 0;">Suivi de votre dossier</p>
            </div>
          </div>
        </div>

        <dl class="fs-definitions fs-mt-6">
          <div><dt>Titres</dt><dd><code>"Segoe UI", system-ui, -apple-system, sans-serif</code></dd></div>
          <div><dt>Texte</dt><dd><code>"Segoe UI", system-ui, -apple-system, sans-serif</code></dd></div>
          <div><dt>Chasse fixe</dt><dd><code>ui-monospace, "Cascadia Mono", Consolas, Menlo, monospace</code></dd></div>
        </dl>
      </div>

      <div class="doc-bloc">
        <h3>Licence et provenance</h3>
        <p class="doc-bloc-note">
          Une administration doit pouvoir répondre à une question simple&nbsp;: a-t-elle le
          droit d'embarquer ces polices dans un site, un document ou une application&nbsp;?
          Les deux familles sont diffusées sous <strong>SIL Open Font License</strong>,
          qui autorise l'usage, la modification et la redistribution, y compris
          commerciale, à condition de ne pas les vendre seules et de conserver le texte
          de la licence.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Famille</th><th>Auteurs</th><th>Licence</th><th>Texte</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Montserrat</td>
                <td>The Montserrat Project Authors</td>
                <td>SIL OFL 1.1</td>
                <td><a href="{{ ressource('assets/polices/montserrat-OFL.txt') }}">montserrat-OFL.txt</a></td>
              </tr>
              <tr>
                <td>Roboto</td>
                <td>The Roboto Project Authors</td>
                <td>SIL OFL 1.1</td>
                <td><a href="{{ ressource('assets/polices/roboto-OFL.txt') }}">roboto-OFL.txt</a></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-note fs-mt-6">
          Le texte de chaque licence accompagne les fichiers dans
          <code>assets/polices/</code>. Il doit être conservé lors de toute reprise du
          système par un projet&nbsp;: c'est la seule obligation que la licence impose.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Échelle typographique</h3>
        <p class="doc-bloc-note">
          Le guide fixe trois tailles par défaut (§&nbsp;2.4.1.3)&nbsp;: 14&nbsp;px pour
          le texte courant (<code>--taille-md</code>), 12&nbsp;px pour les liens de
          navigation et les menus (<code>--taille-menu</code>), 2,5&nbsp;rem pour les
          grands titres. Les tailles de titre sont fluides&nbsp;: elles s'interpolent
          entre 360&nbsp;px et 1280&nbsp;px de largeur de fenêtre, ce qui évite les
          paliers brutaux sur les formats intermédiaires, et le titre de rang&nbsp;1
          atteint les 2,5&nbsp;rem (40&nbsp;px) sur un écran de bureau. Le corps de
          texte reste fixe.
        </p>

        <div class="doc-demo" data-libelle="Spécimen">
          <div class="doc-demo-scene doc-demo-scene--pile" style="display:block;">
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Titre 1</b>Montserrat 800<br>32 → 40&nbsp;px<br>interligne 1,15</div>
              <div class="doc-specimen-rendu"><h1 style="margin:0;">Demander un acte de naissance</h1></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Titre 2</b>Montserrat 700<br>26 → 38&nbsp;px<br>interligne 1,25</div>
              <div class="doc-specimen-rendu"><h2 style="margin:0; border:0; padding:0;">Pièces à fournir</h2></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Titre 3</b>Montserrat 700<br>22 → 29&nbsp;px</div>
              <div class="doc-specimen-rendu"><h3 style="margin:0;">Délai de traitement</h3></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Titre 4</b>Montserrat 600<br>19 → 23&nbsp;px</div>
              <div class="doc-specimen-rendu"><h4 style="margin:0;">Administration responsable</h4></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Chapeau</b>Roboto 400<br>17&nbsp;px<br>interligne 1,75</div>
              <div class="doc-specimen-rendu"><p class="fs-chapeau" style="margin:0;">La demande peut être déposée en ligne ou au guichet de la mairie de résidence.</p></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Corps</b>Roboto 400<br>14&nbsp;px<br>interligne 1,6</div>
              <div class="doc-specimen-rendu"><p style="margin:0;">Le récépissé de dépôt est délivré immédiatement après validation du formulaire. Il ne vaut pas acte et ne peut être produit comme pièce justificative.</p></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Menu</b>Roboto 500<br>12&nbsp;px<br>liens de navigation, menus</div>
              <div class="doc-specimen-rendu"><p style="margin:0; font-size: var(--taille-menu); font-weight: var(--graisse-moyenne);">Démarches en ligne</p></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Petit texte</b>Roboto 400<br>13&nbsp;px</div>
              <div class="doc-specimen-rendu"><p class="fs-legende" style="margin:0;">Mention légale, légende de tableau, précision secondaire.</p></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Surtitre</b>Montserrat 700<br>12&nbsp;px<br>majuscule initiale seule</div>
              <div class="doc-specimen-rendu"><span class="fs-surtitre" style="margin:0;">Service en ligne</span></div>
            </div>
            <div class="doc-specimen">
              <div class="doc-specimen-fiche"><b>Monospace</b>Pile système<br>Références, montants, codes</div>
              <div class="doc-specimen-rendu"><code>BF-2026-004871-OUA</code></div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Bornes de taille et masques de saisie</h3>
        <p class="doc-bloc-note">
          Le guide conseille, pour le texte, une taille comprise entre 8 et
          16&nbsp;points (§&nbsp;2.4, point&nbsp;1)&nbsp;: en dessous, les caractères sont
          quasiment illisibles&nbsp;; au-dessus, ils gênent la lecture. Les titres
          relèvent de leur propre échelle, plafonnée à 2,5&nbsp;rem
          (§&nbsp;2.4.1.3).
        </p>

        <dl class="fs-definitions">
          <div>
            <dt>Minimum</dt>
            <dd>8&nbsp;points, soit environ 11&nbsp;px à l'écran. C'est le plancher de l'échelle (<code>--taille-2xs</code>)&nbsp;: aucun texte de la charte ne descend en dessous, et ce palier est réservé aux mentions secondaires.</dd>
          </div>
          <div>
            <dt>Maximum</dt>
            <dd>16&nbsp;points, soit environ 21&nbsp;px. Le plus grand texte courant de la charte, le chapeau, fait 17&nbsp;px.</dd>
          </div>
          <div>
            <dt>Par défaut</dt>
            <dd>14&nbsp;px pour le texte, 12&nbsp;px pour les liens de navigation et les menus, 2,5&nbsp;rem au plus pour les grands titres.</dd>
          </div>
        </dl>

        <h4 class="fs-mt-8">Masques de saisie</h4>
        <p class="doc-bloc-note">
          Pour une date, un montant ou un numéro de téléphone, le format attendu doit
          être explicite (guide, §&nbsp;2.4.1.4). Dans la charte, il l'est par le
          composant lui-même plutôt que par un gabarit de caractères tapés dans le
          champ&nbsp;: la date se saisit en trois champs jour, mois, année, avec un
          exemple concret dans l'aide&nbsp;; le montant porte son unité
          (<code>F&nbsp;CFA</code>) dans le libellé et en affixe&nbsp;; le numéro de
          téléphone affiche l'indicatif <code>+226</code> en affixe et se lit par
          paires de chiffres. Voir les
          <a href="{{ page('composants') }}#formulaires">composants de formulaire</a>.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Règles de composition</h3>
        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Limiter le texte courant à environ 68 caractères par ligne (<code>--mesure</code>).</li>
              <li>Respecter la hiérarchie des niveaux de titre sans en sauter&nbsp;: un <code>h3</code> suit un <code>h2</code>.</li>
              <li>Aligner les chiffres en colonne avec <code>font-variant-numeric: tabular-nums</code> dans les tableaux et les montants.</li>
              <li>Écrire les montants en francs CFA avec séparateur de milliers et unité explicite.</li>
              <li>Ne mettre une majuscule qu'à l'initiale du premier mot d'un libellé, d'une option de menu, d'un bouton ou d'un titre&nbsp;: «&nbsp;Pièces à fournir&nbsp;», pas «&nbsp;Pièces À Fournir&nbsp;» (guide, §&nbsp;2.4, point&nbsp;4). Les noms propres et les sigles gardent leurs capitales.</li>
              <li>Une seule police par champ. Un codage particulier (graisse, couleur, soulignement, chasse fixe) reste admis pour distinguer un mot clé, un lien ou le libellé d'un champ obligatoire (guide, §&nbsp;2.4, point&nbsp;3).</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Composer en capitales un libellé, un titre ou un paragraphe. Seul le nom «&nbsp;BURKINA FASO&nbsp;» du bloc-marque, qui est un logotype, reste en capitales.</li>
              <li>Employer l'italique, y compris pour une citation ou une mise en valeur&nbsp;: il nuit à la lisibilité à l'écran (guide, §&nbsp;2.4, point&nbsp;2).</li>
              <li>Justifier le texte&nbsp;: sans césure, la justification crée des lézardes ingérables.</li>
              <li>Descendre sous 14&nbsp;px pour un contenu que l'usager doit lire pour agir.</li>
              <li>Introduire une troisième famille typographique pour un besoin ponctuel.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="images">
      <h2>Images</h2>
      <p class="doc-section-intro">
        Une image de service public informe&nbsp;; elle n'habille pas. La question à se
        poser avant d'en placer une n'est pas «&nbsp;qu'est-ce qui irait bien ici&nbsp;?&nbsp;» mais
        «&nbsp;qu'est-ce que l'usager comprendra mieux grâce à elle&nbsp;?&nbsp;». Si la réponse
        n'est pas évidente, la place reste vide. Le guide le dit à sa
        manière&nbsp;: chaque prise de vue doit traduire la volonté de
        <strong>communiquer par la preuve</strong> (guide, §&nbsp;4.2.1.1).
      </p>

      <div class="doc-bloc">
        <h3>Rôle de l'image</h3>
        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">Légitime</p>
            <ul>
              <li>Montrer à quoi ressemble un document ou un guichet, pour que l'usager le reconnaisse.</li>
              <li>Situer un lieu&nbsp;: façade d'une mairie, plan d'accès à un centre d'enrôlement.</li>
              <li>Illustrer un geste&nbsp;: comment cadrer la photographie d'une pièce d'identité.</li>
              <li>Documenter une actualité par une photographie de l'événement.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Photographie d'ambiance pour «&nbsp;aérer&nbsp;» une page de démarche.</li>
              <li>Banque d'images internationale sans lien avec le Burkina Faso.</li>
              <li>Image de fond derrière du texte. Le contraste devient incontrôlable.</li>
              <li>Illustration décorative dans un parcours de démarche. Elle allonge la page et distrait de l'action.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Représentation des personnes</h3>
        <p class="doc-bloc-note">
          Les images d'un service de l'État montrent les usagers tels qu'ils sont,
          sans mise en scène. Cette exigence n'est pas cosmétique. Un usager qui ne
          se reconnaît pas dans les images d'un service doute de s'y adresser.
        </p>

        <ul class="fs-liste-controle fs-mesure">
          <li><strong>Dignité.</strong> Aucune mise en scène de détresse, de pauvreté ou de dépendance. L'usager d'un service public est un citoyen qui exerce un droit.</li>
          <li><strong>Diversité réelle.</strong> Âges, genres, régions, milieux urbains et ruraux, situations de handicap. Une administration nationale s'adresse à tout le pays.</li>
          <li><strong>Justesse.</strong> Les tenues, les lieux et les documents montrés sont ceux du Burkina Faso.</li>
          <li><strong>Consentement.</strong> Autorisation écrite pour toute personne identifiable, conservée par l'administration émettrice.</li>
          <li><strong>Sobriété.</strong> Aucun sourire commercial, aucune pose professionnelle appuyée.</li>
        </ul>
      </div>

      <div class="doc-bloc">
        <h3>Prises de vue et cadrages</h3>
        <p class="doc-bloc-note">
          La simplicité et l'efficacité des clichés se recherchent systématiquement,
          dans le cadrage comme dans la mise en scène (guide, §&nbsp;4.2.1).
        </p>

        <dl class="fs-definitions">
          <div>
            <dt>Cadrages</dt>
            <dd>Gros plan, plan rapproché ou plan large, rien d'autre. Une mise en scène épurée&nbsp;: de grandes lignes, le moins d'éléments possible, pour la lisibilité (§&nbsp;4.2.1.2).</dd>
          </div>
          <div>
            <dt>Sujet</dt>
            <dd>Des visuels humains et factuels&nbsp;: des personnes, des lieux, des documents réels. Pas de représentation conceptuelle ou symbolique (§&nbsp;4.2.1.2).</dd>
          </div>
          <div>
            <dt>Couleurs</dt>
            <dd>Des images sans saturation excessive, accordées aux couleurs du logo et au gris de la palette, pour conserver l'unité visuelle (§&nbsp;4.2.2).</dd>
          </div>
        </dl>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À privilégier</p>
            <ul>
              <li>Un traitement épuré, de l'espace libre, des compositions simples.</li>
              <li>Des prises de vue de face.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À éviter</p>
            <ul>
              <li>Les effets de mouvement flou.</li>
              <li>La juxtaposition de plusieurs visuels et les compositions en fondu.</li>
              <li>Les angles de vue en plongée et en contre-plongée.</li>
              <li>Les cadrages aléatoires.</li>
            </ul>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">Principes de composition du guide (§&nbsp;4.2.3, tableau&nbsp;1).</p>
      </div>

      <div class="doc-bloc">
        <h3>Traitement et format <code>.fs-figure</code></h3>
        <p class="doc-bloc-note">
          Le rapport d'affichage est déclaré pour qu'aucun décalage de mise en page ne
          survienne pendant le chargement. La légende porte l'information&nbsp;; le crédit
          la suit, plus discret.
        </p>

        <div class="doc-demo" data-libelle="Figure">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <figure class="fs-figure fs-figure--16-9">
              <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 9'%3E%3Crect width='16' height='9' fill='%23e2ddd6'/%3E%3C/svg%3E"
                   width="16" height="9" alt="Emplacement de la photographie">
              <figcaption>
                Guichet de l'état civil de la mairie de Ouagadougou, ouvert du lundi au
                vendredi de 7&nbsp;h&nbsp;30 à 15&nbsp;h&nbsp;30.
                <span class="fs-figure-credit">Photographie&nbsp;: Service d'information du Gouvernement, 2026</span>
              </figcaption>
            </figure>
          </div>
        </div>

        <dl class="fs-definitions fs-mt-6">
          <div><dt>Rapports admis</dt><dd><code>16/9</code> pour une bannière, <code>4/3</code> pour une photographie de contenu, <code>1/1</code> pour un portrait ou une vignette.</dd></div>
          <div><dt>Traitement</dt><dd>Aucun filtre, aucune teinte de marque, aucun cadre arrondi au-delà du rayon du système. Recadrage seulement&nbsp;: l'accord avec les couleurs du logo et le gris se cherche au choix de l'image, pas par retouche.</dd></div>
          <div><dt>Poids</dt><dd>200&nbsp;Ko au maximum par image, dimensionnée à sa taille d'affichage. Voir le <a href="{{ page('accessibilite') }}#budget">budget de performance</a>.</dd></div>
          <div><dt>Texte de remplacement</dt><dd>Descriptif si l'image porte une information, vide (<code>alt=""</code>) si elle est redondante avec le texte voisin.</dd></div>
        </dl>
      </div>

      <div class="doc-bloc">
        <h3>Formats et optimisation</h3>
        <p class="doc-bloc-note">
          Un bon rapport qualité/poids améliore l'accessibilité d'une page (guide,
          §&nbsp;4.3). Le guide date de 2024 mais reprend des recommandations plus
          anciennes&nbsp;; la charte en garde l'intention et adapte deux points au web
          actuel.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Format à employer selon la nature de l'image</caption>
            <thead>
              <tr><th>Nature</th><th>Format</th><th>Réglage</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Photographie</td>
                <td>JPEG, ou WebP</td>
                <td>Qualité autour de 75&nbsp;%, le compromis que retient le guide. Le WebP est admis comme alternative, à qualité comparable pour un poids moindre.</td>
              </tr>
              <tr>
                <td>Logo, pictogramme, schéma</td>
                <td>SVG</td>
                <td>Vectoriel, net à toutes les tailles et à toutes les densités d'écran. C'est le format de l'emblème et des icônes de la charte.</td>
              </tr>
              <tr>
                <td>Vignette, capture, graphique matriciel</td>
                <td>PNG</td>
                <td>Compression sans perte et transparence. C'est le repli quand le vectoriel n'est pas rendu, en bureautique ou dans un courriel.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-note fs-mt-6">
          <strong>Ce que la charte adapte.</strong> Le guide recommande le GIF pour les
          logotypes et les pictogrammes&nbsp;: ses 256 couleurs le rendent léger, mais
          il pixellise à l'agrandissement et gère mal la transparence. Le SVG, que le
          guide préconise lui-même pour les icônes (§&nbsp;4.1), et le PNG le
          remplacent. Le guide fixe aussi une résolution de 72&nbsp;dpi&nbsp;: cette
          valeur n'a aucun effet sur l'affichage web. On raisonne en pixels&nbsp;: une
          image est servie aux dimensions où elle s'affiche, au double au plus pour
          les écrans haute densité.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Illustration</h3>
        <p class="doc-bloc-note">
          L'illustration n'intervient que là où la photographie ne peut rien montrer&nbsp;:
          un schéma de procédure, une page d'erreur, un état vide. Son dessin dérive
          du jeu d'icônes&nbsp;— trait ouvert, épaisseur constante, aucune forme pleine&nbsp;—
          de sorte qu'elle appartient visiblement au même système.
        </p>

        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Reprendre la grille, l'épaisseur de trait et les extrémités arrondies du jeu d'icônes.</li>
              <li>Se limiter aux couleurs du système, l'or servant d'unique accent.</li>
              <li>Garder l'illustration lisible en monochrome.</li>
              <li>La traiter comme décorative&nbsp;: <code>aria-hidden</code>, l'information restant dans le texte.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Personnages stylisés aux proportions fantaisistes.</li>
              <li>Illustration portant seule une information ou une instruction.</li>
              <li>Dégradés, ombres portées, perspective.</li>
              <li>Mélange avec des illustrations d'une autre provenance.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="grille">
      <h2>Grille et mise en page</h2>
      <p class="doc-section-intro">
        Une grille de douze colonnes, une gouttière fluide et quatre largeurs de
        conteneur couvrent l'ensemble des gabarits. La conception part du format le
        plus étroit, celui de l'usage dominant.
      </p>

      <div class="doc-bloc">
        <h3>Colonnes <code>.fs-grille</code></h3>
        <div class="doc-demo" data-libelle="Grille de 12 colonnes">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="doc-grille-demo">
              <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div>
              <div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Répartitions courantes</h3>
        <p class="doc-bloc-note">
          Les classes <code>.fs-col-*</code> portent le nombre de colonnes occupées.
          Sous 768&nbsp;px, toutes les colonnes passent en pleine largeur, sans rien
          déclarer de plus.
        </p>

        <div class="doc-demo" data-libelle="Répartitions">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-grille">
              <div class="fs-col-8 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">Contenu principal&nbsp;— 8 colonnes</div>
              <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">Colonne latérale&nbsp;— 4</div>
            </div>

            <div class="fs-grille">
              <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">4</div>
              <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">4</div>
              <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">4</div>
            </div>

            <div class="fs-grille">
              <div class="fs-col-6 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">6</div>
              <div class="fs-col-3 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">3</div>
              <div class="fs-col-3 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">3</div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Contenu et colonne latérale <code>.fs-avec-aside</code></h3>
        <p class="doc-bloc-note">
          Assemblage le plus fréquent des gabarits institutionnels. La colonne
          latérale mesure 300&nbsp;px et passe sous le contenu principal en dessous de
          1024&nbsp;px. La variante <code>.fs-avec-aside--gauche</code> place une colonne
          de navigation de 260&nbsp;px avant le contenu.
        </p>

        <div class="doc-demo" data-libelle="Contenu et colonne latérale">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-avec-aside">
              <div class="fs-fond-enfoncee" style="padding: var(--espace-6); border-radius: var(--rayon-md);">Contenu principal</div>
              <aside class="fs-fond-enfoncee" style="padding: var(--espace-6); border-radius: var(--rayon-md);">Colonne latérale</aside>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Largeurs de conteneur</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Classe</th><th>Largeur</th><th>Emploi</th></tr>
            </thead>
            <tbody>
              <tr><td><code>.fs-conteneur--etroit</code></td><td class="fs-tabulaire">560&nbsp;px</td><td>Formulaire d'authentification, confirmation, message isolé</td></tr>
              <tr><td><code>.fs-conteneur--texte</code></td><td class="fs-tabulaire">760&nbsp;px</td><td>Article, contenu réglementaire, fiche de démarche</td></tr>
              <tr><td><code>.fs-conteneur</code></td><td class="fs-tabulaire">1200&nbsp;px</td><td>Largeur de référence de la majorité des pages</td></tr>
              <tr><td><code>.fs-conteneur--large</code></td><td class="fs-tabulaire">1440&nbsp;px</td><td>Tableau de bord, liste dense, tableau à nombreuses colonnes</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Points de rupture</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Palier</th><th>Largeur</th><th>Adaptation</th></tr>
            </thead>
            <tbody>
              <tr><td>Étroit</td><td class="fs-tabulaire">&lt; 560&nbsp;px</td><td>Colonne unique, actions en pleine largeur, modale plein écran</td></tr>
              <tr><td>Intermédiaire</td><td class="fs-tabulaire">560 – 768&nbsp;px</td><td>Colonne unique, tableaux empilés</td></tr>
              <tr><td>Tablette</td><td class="fs-tabulaire">768 – 1024&nbsp;px</td><td>Deux colonnes, colonne latérale repliée sous le contenu</td></tr>
              <tr><td>Bureau</td><td class="fs-tabulaire">&gt; 1024&nbsp;px</td><td>Grille complète, colonne latérale visible</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="espacement">
      <h2>Espacement</h2>
      <p class="doc-section-intro">
        Une échelle unique de base 4&nbsp;px. Une valeur prise hors de cette échelle
        crée des désalignements qui se propagent dans toute l'interface.
      </p>

      <div class="doc-bloc">
        <h3>Échelle</h3>
        <div class="doc-demo" data-libelle="Échelle d'espacement">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="doc-espaces">
              <div class="doc-espace"><span class="doc-espace-nom">--espace-1</span><span class="doc-espace-barre" style="width:4px"></span><span class="doc-espace-val">4&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-2</span><span class="doc-espace-barre" style="width:8px"></span><span class="doc-espace-val">8&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-3</span><span class="doc-espace-barre" style="width:12px"></span><span class="doc-espace-val">12&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-4</span><span class="doc-espace-barre" style="width:16px"></span><span class="doc-espace-val">16&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-5</span><span class="doc-espace-barre" style="width:20px"></span><span class="doc-espace-val">20&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-6</span><span class="doc-espace-barre" style="width:24px"></span><span class="doc-espace-val">24&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-8</span><span class="doc-espace-barre" style="width:32px"></span><span class="doc-espace-val">32&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-10</span><span class="doc-espace-barre" style="width:40px"></span><span class="doc-espace-val">40&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-12</span><span class="doc-espace-barre" style="width:48px"></span><span class="doc-espace-val">48&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-16</span><span class="doc-espace-barre" style="width:64px"></span><span class="doc-espace-val">64&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-20</span><span class="doc-espace-barre" style="width:80px"></span><span class="doc-espace-val">80&nbsp;px</span></div>
              <div class="doc-espace"><span class="doc-espace-nom">--espace-24</span><span class="doc-espace-barre" style="width:96px"></span><span class="doc-espace-val">96&nbsp;px</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Emplois de référence</h3>
        <dl class="fs-definitions">
          <div><dt>4 – 8&nbsp;px</dt><dd>Écart entre une étiquette et son champ, entre une icône et son libellé.</dd></div>
          <div><dt>12 – 16&nbsp;px</dt><dd>Rembourrage interne des composants, écart entre champs d'un même groupe.</dd></div>
          <div><dt>24 – 32&nbsp;px</dt><dd>Rembourrage des cartes, écart entre groupes de champs, gouttière de grille.</dd></div>
          <div><dt>48 – 96&nbsp;px</dt><dd>Respiration entre sections de page, variable selon la largeur de fenêtre.</dd></div>
        </dl>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="formes">
      <h2>Formes et profondeur</h2>
      <p class="doc-section-intro">
        Les rayons restent sobres&nbsp;: un arrondi prononcé évoque le registre
        promotionnel, étranger au service public. Les ombres sont teintées chaud pour
        rester cohérentes avec les neutres, et servent à hiérarchiser les plans, pas
        à décorer.
      </p>

      <div class="doc-bloc">
        <h3>Rayons de bordure</h3>
        <div class="doc-demo" data-libelle="Rayons">
          <div class="doc-demo-scene">
            <div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-sm)"></div><span class="doc-variante-nom">sm · 4&nbsp;px</span></div>
            <div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-md)"></div><span class="doc-variante-nom">md · 6&nbsp;px</span></div>
            <div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-lg)"></div><span class="doc-variante-nom">lg · 10&nbsp;px</span></div>
            <div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-xl)"></div><span class="doc-variante-nom">xl · 16&nbsp;px</span></div>
            <div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-plein)"></div><span class="doc-variante-nom">plein</span></div>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Correspondances&nbsp;: <code>md</code> pour les boutons et les champs,
          <code>lg</code> pour les cartes et les tableaux, <code>xl</code> pour les
          modales et les blocs de démarche, <code>plein</code> pour les étiquettes
          et les pastilles.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Élévation</h3>
        <div class="doc-demo" data-libelle="Ombres">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="doc-ombres">
              <div class="doc-ombre" style="box-shadow: var(--ombre-xs)">xs</div>
              <div class="doc-ombre" style="box-shadow: var(--ombre-sm)">sm</div>
              <div class="doc-ombre" style="box-shadow: var(--ombre-md)">md</div>
              <div class="doc-ombre" style="box-shadow: var(--ombre-lg)">lg</div>
              <div class="doc-ombre" style="box-shadow: var(--ombre-xl)">xl</div>
            </div>
          </div>
        </div>
        <dl class="fs-definitions fs-mt-6">
          <div><dt>xs&nbsp;— repos</dt><dd>Bouton principal au repos. Suggère la possibilité d'une action sans détacher l'élément.</dd></div>
          <div><dt>sm&nbsp;— survol</dt><dd>Carte ou bouton survolé.</dd></div>
          <div><dt>md&nbsp;— flottant</dt><dd>Carte détachée, infobulle.</dd></div>
          <div><dt>lg&nbsp;— superposé</dt><dd>Menu déroulant, notification passagère.</dd></div>
          <div><dt>xl&nbsp;— modal</dt><dd>Modale, panneau latéral, bandeau de consentement.</dd></div>
        </dl>
      </div>

      <div class="doc-bloc">
        <h3>Focus</h3>
        <p class="doc-bloc-note">
          Un seul traitement de focus pour tout le système, appliqué via
          <code>:focus-visible</code>. Il n'est jamais désactivé&nbsp;: une interface sans
          anneau de focus est inutilisable au clavier, ce qui exclut une part des
          usagers et contrevient aux obligations d'accessibilité.
        </p>
        <div class="doc-demo" data-libelle="Anneau de focus">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--principal">Naviguez au clavier jusqu'ici</button>
            <a class="fs-btn fs-btn--tertiaire" href="#focus">Puis jusqu'ici</a>
            <input class="fs-saisie" style="max-width: 220px;" type="text" aria-label="Exemple de champ" placeholder="Et enfin ici">
          </div>
        </div>
      </div>
    </section>


    <!-- ===================================================== -->
    <section class="doc-section" id="iconographie">
      <h2>Iconographie</h2>
      <p class="doc-section-intro">
        Une icône ne remplace jamais un mot&nbsp;: elle accélère la reconnaissance d'un
        libellé déjà présent. Le jeu est volontairement restreint&nbsp;— 35 tracés
        couvrant les besoins réels des parcours documentés dans cette charte. Un
        service à qui il manque une icône en propose l'ajout plutôt que d'en importer
        une d'un jeu tiers, dont le dessin jurerait avec l'ensemble.
      </p>

      <div class="doc-bloc">
        <h3>Principes du guide</h3>
        <p class="doc-bloc-note">
          Une icône a deux fonctions&nbsp;: aider à identifier la nature d'une
          information, ou représenter l'action d'un bouton de commande (guide,
          §&nbsp;4.1). Elle doit être explicite, c'est-à-dire représentative de l'action
          ou du concept qu'elle désigne, et constante&nbsp;: une même action ou un même
          concept garde la même icône dans toute l'application, jamais deux icônes
          pour une seule chose.
        </p>
        <div class="doc-note">
          Le guide préconise des images vectorielles et suggère les jeux de Font
          Awesome et d'Icones8. La charte fournit son propre jeu vectoriel, servi
          depuis le domaine du service&nbsp;: pas de dépendance à un tiers, et un dessin
          homogène avec l'emblème et les composants. Les principes du guide s'y
          appliquent tels quels.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Construction</h3>
        <p class="doc-bloc-note">
          Toutes les icônes partagent la même grille et la même épaisseur de trait.
          C'est cette régularité, bien plus que le dessin de chaque symbole, qui fait
          tenir un jeu d'icônes.
        </p>

        <div class="doc-demo" data-libelle="Grille de construction">
          <div class="doc-demo-scene">
            <div class="doc-grille-icone">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <rect class="doc-grille-zone" x="2" y="2" width="20" height="20" rx="1"/>
                <path class="doc-grille-trait" d="M12 0v24M0 12h24"/>
                <circle class="doc-grille-trait" cx="12" cy="12" r="9"/>
                <g class="doc-grille-dessin">
                  <circle cx="11" cy="11" r="6.5"/>
                  <path d="M15.9 15.9 20.5 20.5"/>
                </g>
              </svg>
            </div>
            <dl class="fs-definitions" style="flex:1; min-width: 260px;">
              <div><dt>Grille</dt><dd>24 × 24, zone utile 20 × 20</dd></div>
              <div><dt>Trait</dt><dd>1,5 — constant, jamais mis à l'échelle</dd></div>
              <div><dt>Extrémités</dt><dd>Arrondies, jonctions arrondies</dd></div>
              <div><dt>Style</dt><dd>Trait ouvert, aucune forme pleine, aucune couleur</dd></div>
            </dl>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Mise en œuvre <code>.fs-icone</code></h3>
        <p class="doc-bloc-note">
          Chaque tracé est appliqué en masque CSS sur un aplat de
          <code>currentColor</code>&nbsp;: une icône hérite donc toujours de la couleur
          du texte qui l'accompagne et suit le thème sans le moindre réglage. Sa
          taille est exprimée en <code>em</code>, donc proportionnelle au corps du
          texte voisin.
        </p>

        <div class="doc-demo" data-libelle="Tailles et héritage de couleur">
          <div class="doc-demo-scene">
            <span class="fs-icone fs-icone--dossier fs-icone--sm" aria-hidden="true"></span>
            <span class="fs-icone fs-icone--dossier" aria-hidden="true"></span>
            <span class="fs-icone fs-icone--dossier fs-icone--lg" aria-hidden="true"></span>
            <span class="fs-icone fs-icone--dossier fs-icone--xl" aria-hidden="true"></span>
            <span class="fs-icone fs-icone--dossier fs-icone--xxl" aria-hidden="true"></span>
            <span style="color: var(--accent-sur-clair);">
              <span class="fs-icone fs-icone--dossier fs-icone--xl" aria-hidden="true"></span>
            </span>
            <span style="color: var(--action-sur-clair);">
              <span class="fs-icone fs-icone--dossier fs-icone--xl" aria-hidden="true"></span>
            </span>
          </div>
        </div>

        <div class="doc-demo fs-mt-6" data-libelle="Dans les composants">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--principal">
              <span class="fs-icone fs-icone--televerser" aria-hidden="true"></span>
              Déposer une pièce
            </button>
            <a class="fs-btn fs-btn--tertiaire" href="#">
              <span class="fs-icone fs-icone--telecharger" aria-hidden="true"></span>
              Télécharger le récépissé
            </a>
            <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--icone" aria-label="Imprimer la fiche">
              <span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span>
            </button>
            <span class="fs-badge fs-badge--succes">
              <span class="fs-icone fs-icone--succes fs-icone--sm" aria-hidden="true"></span>
              Validé
            </span>
            <span class="fs-statut fs-statut--cours">
              <span class="fs-icone fs-icone--horloge fs-icone--sm" aria-hidden="true"></span>
              En cours d'instruction
            </span>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Accessibilité</h3>
        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Porter <code>aria-hidden="true"</code> sur toute icône accompagnant un libellé&nbsp;: elle est décorative et ne doit pas être annoncée deux fois.</li>
              <li>Sur un bouton réduit à une icône, donner le nom accessible par <code>aria-label</code> posé sur le bouton.</li>
              <li>Conserver la cible tactile de 44&nbsp;px même lorsque l'icône n'en fait que 20.</li>
              <li>Doubler toute icône porteuse de sens par un texte, y compris dans les tableaux denses.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Employer une icône seule pour signaler un état&nbsp;: elle tombe sous le principe «&nbsp;la couleur ne porte jamais seule&nbsp;».</li>
              <li>Redessiner ou étirer un tracé&nbsp;: l'épaisseur du trait ne se met jamais à l'échelle.</li>
              <li>Mélanger ce jeu avec des icônes d'une autre provenance.</li>
              <li>Employer une icône comme puce décorative répétée dans un texte courant.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Le jeu complet</h3>
        <p class="doc-bloc-note">
          Les icônes sont classées par usage et non par forme. On cherche «&nbsp;ce que
          je veux dire&nbsp;», pas «&nbsp;ce que ça représente&nbsp;». Le fichier
          <code>assets/img/icones.svg</code> rassemble les mêmes tracés pour les
          outils de conception.
        </p>

        <h4 class="fs-mt-8">Navigation</h4>
        <p class="fs-legende" style="margin-bottom: var(--espace-4);">11 icônes. Cliquez sur une icône pour copier sa classe.</p>
        <div class="doc-icones">
            <div class="doc-icone" data-copier="fs-icone--recherche" title="Recherche, loupe">
              <span class="fs-icone fs-icone--recherche" aria-hidden="true"></span>
              <span class="doc-icone-nom">recherche</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--fermer" title="Fermer une modale, retirer une étiquette">
              <span class="fs-icone fs-icone--fermer" aria-hidden="true"></span>
              <span class="doc-icone-nom">fermer</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--menu" title="Ouvrir la navigation sur écran étroit">
              <span class="fs-icone fs-icone--menu" aria-hidden="true"></span>
              <span class="doc-icone-nom">menu</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--chevron-bas" title="Accordéon, liste déroulante">
              <span class="fs-icone fs-icone--chevron-bas" aria-hidden="true"></span>
              <span class="doc-icone-nom">chevron-bas</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--chevron-haut" title="Replier une section">
              <span class="fs-icone fs-icone--chevron-haut" aria-hidden="true"></span>
              <span class="doc-icone-nom">chevron-haut</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--chevron-droite" title="Fil d'Ariane, page suivante">
              <span class="fs-icone fs-icone--chevron-droite" aria-hidden="true"></span>
              <span class="doc-icone-nom">chevron-droite</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--chevron-gauche" title="Retour, page précédente">
              <span class="fs-icone fs-icone--chevron-gauche" aria-hidden="true"></span>
              <span class="doc-icone-nom">chevron-gauche</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--fleche-droite" title="Lien d'action, étape suivante">
              <span class="fs-icone fs-icone--fleche-droite" aria-hidden="true"></span>
              <span class="doc-icone-nom">fleche-droite</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--lien-externe" title="Lien quittant le service">
              <span class="fs-icone fs-icone--lien-externe" aria-hidden="true"></span>
              <span class="doc-icone-nom">lien-externe</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--filtre" title="Filtrer une liste de résultats">
              <span class="fs-icone fs-icone--filtre" aria-hidden="true"></span>
              <span class="doc-icone-nom">filtre</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--trier" title="Trier une colonne de tableau">
              <span class="fs-icone fs-icone--trier" aria-hidden="true"></span>
              <span class="doc-icone-nom">trier</span>
            </div>
        </div>

        <h4 class="fs-mt-8">Démarches</h4>
        <p class="fs-legende" style="margin-bottom: var(--espace-4);">8 icônes. Cliquez sur une icône pour copier sa classe.</p>
        <div class="doc-icones">
            <div class="doc-icone" data-copier="fs-icone--document" title="Pièce à fournir, fiche de démarche">
              <span class="fs-icone fs-icone--document" aria-hidden="true"></span>
              <span class="doc-icone-nom">document</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--dossier" title="Dossier en cours d'instruction">
              <span class="fs-icone fs-icone--dossier" aria-hidden="true"></span>
              <span class="doc-icone-nom">dossier</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--telecharger" title="Récépissé, quittance, document délivré">
              <span class="fs-icone fs-icone--telecharger" aria-hidden="true"></span>
              <span class="doc-icone-nom">telecharger</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--televerser" title="Déposer une pièce justificative">
              <span class="fs-icone fs-icone--televerser" aria-hidden="true"></span>
              <span class="doc-icone-nom">televerser</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--imprimer" title="Imprimer une fiche ou un récépissé">
              <span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span>
              <span class="doc-icone-nom">imprimer</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--calendrier" title="Date de dépôt, échéance">
              <span class="fs-icone fs-icone--calendrier" aria-hidden="true"></span>
              <span class="doc-icone-nom">calendrier</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--horloge" title="Délai de traitement, horaires">
              <span class="fs-icone fs-icone--horloge" aria-hidden="true"></span>
              <span class="doc-icone-nom">horloge</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--paiement" title="Coût, droit de timbre, règlement">
              <span class="fs-icone fs-icone--paiement" aria-hidden="true"></span>
              <span class="doc-icone-nom">paiement</span>
            </div>
        </div>

        <h4 class="fs-mt-8">Identité</h4>
        <p class="fs-legende" style="margin-bottom: var(--espace-4);">6 icônes. Cliquez sur une icône pour copier sa classe.</p>
        <div class="doc-icones">
            <div class="doc-icone" data-copier="fs-icone--carte-identite" title="CNIB, pièce d'identité">
              <span class="fs-icone fs-icone--carte-identite" aria-hidden="true"></span>
              <span class="doc-icone-nom">carte-identite</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--utilisateur" title="Compte usager, espace personnel">
              <span class="fs-icone fs-icone--utilisateur" aria-hidden="true"></span>
              <span class="doc-icone-nom">utilisateur</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--institution" title="Administration responsable, ministère">
              <span class="fs-icone fs-icone--institution" aria-hidden="true"></span>
              <span class="doc-icone-nom">institution</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--lieu" title="Guichet, mairie, point de retrait">
              <span class="fs-icone fs-icone--lieu" aria-hidden="true"></span>
              <span class="doc-icone-nom">lieu</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--telephone" title="Contact téléphonique, numéro vert">
              <span class="fs-icone fs-icone--telephone" aria-hidden="true"></span>
              <span class="doc-icone-nom">telephone</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--courriel" title="Contact électronique, récépissé envoyé">
              <span class="fs-icone fs-icone--courriel" aria-hidden="true"></span>
              <span class="doc-icone-nom">courriel</span>
            </div>
          </div>

        <h4 class="fs-mt-8">États</h4>
        <p class="fs-legende" style="margin-bottom: var(--espace-4);">6 icônes. Cliquez sur une icône pour copier sa classe.</p>
        <div class="doc-icones">
            <div class="doc-icone" data-copier="fs-icone--information" title="Encart d'information neutre">
              <span class="fs-icone fs-icone--information" aria-hidden="true"></span>
              <span class="doc-icone-nom">information</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--succes" title="Dossier validé, opération aboutie">
              <span class="fs-icone fs-icone--succes" aria-hidden="true"></span>
              <span class="doc-icone-nom">succes</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--alerte" title="Mise en garde, complément demandé">
              <span class="fs-icone fs-icone--alerte" aria-hidden="true"></span>
              <span class="doc-icone-nom">alerte</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--erreur" title="Erreur de saisie, demande rejetée">
              <span class="fs-icone fs-icone--erreur" aria-hidden="true"></span>
              <span class="doc-icone-nom">erreur</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--coche" title="Élément validé dans une liste de contrôle">
              <span class="fs-icone fs-icone--coche" aria-hidden="true"></span>
              <span class="doc-icone-nom">coche</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--aide" title="Aide contextuelle, question fréquente">
              <span class="fs-icone fs-icone--aide" aria-hidden="true"></span>
              <span class="doc-icone-nom">aide</span>
            </div>
        </div>

        <h4 class="fs-mt-8">Confiance</h4>
        <p class="fs-legende" style="margin-bottom: var(--espace-4);">4 icônes. Cliquez sur une icône pour copier sa classe.</p>
        <div class="doc-icones">
            <div class="doc-icone" data-copier="fs-icone--cadenas" title="Connexion sécurisée, donnée protégée">
              <span class="fs-icone fs-icone--cadenas" aria-hidden="true"></span>
              <span class="doc-icone-nom">cadenas</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--bouclier" title="Service officiel vérifié">
              <span class="fs-icone fs-icone--bouclier" aria-hidden="true"></span>
              <span class="doc-icone-nom">bouclier</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--signalement" title="Signaler une fraude, une page suspecte">
              <span class="fs-icone fs-icone--signalement" aria-hidden="true"></span>
              <span class="doc-icone-nom">signalement</span>
            </div>
            <div class="doc-icone" data-copier="fs-icone--oeil" title="Afficher un contenu masqué">
              <span class="fs-icone fs-icone--oeil" aria-hidden="true"></span>
              <span class="doc-icone-nom">oeil</span>
            </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="mouvement">
      <h2>Mouvement</h2>
      <p class="doc-section-intro">
        L'animation sert à expliquer un changement d'état, jamais à séduire. Les
        durées sont courtes&nbsp;: sur un appareil modeste, une transition longue est
        perçue comme une lenteur du service, pas comme un raffinement.
      </p>

      <div class="doc-bloc">
        <h3>Durées et courbes</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Jeton</th><th>Durée</th><th>Emploi</th></tr>
            </thead>
            <tbody>
              <tr><td><code>--duree-instant</code></td><td class="fs-tabulaire">80&nbsp;ms</td><td>Enfoncement d'un bouton, retour tactile</td></tr>
              <tr><td><code>--duree-courte</code></td><td class="fs-tabulaire">140&nbsp;ms</td><td>Survol, changement de couleur, focus</td></tr>
              <tr><td><code>--duree-moyenne</code></td><td class="fs-tabulaire">220&nbsp;ms</td><td>Ouverture d'un menu, d'une modale, d'un accordéon</td></tr>
              <tr><td><code>--duree-longue</code></td><td class="fs-tabulaire">360&nbsp;ms</td><td>Panneau latéral, progression d'une jauge</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Préférence de mouvement réduit</h3>
        <p class="doc-bloc-note">
          Lorsque l'usager a demandé une réduction des animations au niveau du
          système, toutes les durées sont ramenées à 1&nbsp;ms et les animations en
          boucle sont supprimées. Cette règle est appliquée dans
          <code>tokens.css</code> et n'a donc pas à être répétée composant par
          composant.
        </p>
        <pre>@verbatim<code>@media (prefers-reduced-motion: reduce) {
  :root {
    --duree-instant: 1ms;
    --duree-courte:  1ms;
    --duree-moyenne: 1ms;
    --duree-longue:  1ms;
  }
}</code>@endverbatim</pre>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="themes">
      <h2>Thèmes</h2>
      <p class="doc-section-intro">
        Le thème sombre n'est pas un ornement. Il réduit la consommation sur écran
        OLED et soulage la lecture de nuit, deux besoins courants pour un usager
        qui consulte un service depuis son téléphone. Il est obtenu en réaffectant
        les rôles sémantiques, sans toucher à un seul composant.
      </p>

      <div class="doc-bloc">
        <h3>Arbitrages du thème sombre</h3>
        <ul class="fs-liste-puces fs-mesure">
          <li>
            Les teintes officielles du drapeau sont éclaircies d'un palier sur fond
            sombre&nbsp;: au palier 500, le vert et le rouge ne dégagent plus un contraste
            suffisant pour porter du texte.
          </li>
          <li>
            Les fonds ne sont jamais noir pur. La base est un brun très sombre,
            cohérent avec la souche chaude des neutres, qui réduit l'éblouissement.
          </li>
          <li>
            Les ombres deviennent plus denses et plus contrastées, car la
            profondeur se lit moins bien sur fond sombre.
          </li>
          <li>
            Le thème clair est le thème par défaut de l'administration, quelle que
            soit la préférence du système d'exploitation. Un service public doit
            présenter le même visage à tous ses usagers. Le thème sombre s'active
            sur choix explicite, et ce choix est conservé d'une visite à l'autre.
          </li>
        </ul>

        <div class="fs-actions fs-mt-6">
          <button type="button" class="fs-btn fs-btn--secondaire" data-bascule-theme aria-pressed="false">
            <span data-theme-libelle>Thème sombre</span>
          </button>
        </div>
      </div>
    </section>

@endsection
