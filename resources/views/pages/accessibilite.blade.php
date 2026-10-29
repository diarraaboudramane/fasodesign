@extends('layouts.documentation')

@section('contenu')

    <div class="doc-entete">
      <nav class="doc-fil" aria-label="Fil d'Ariane">
        <a href="{{ page('index') }}">Charte</a> <span aria-hidden="true">/</span> <span>Accessibilité</span>
      </nav>
      <h1>Accessibilité et rédaction</h1>
      <p class="fs-chapeau">
        Un service public s'adresse à toute la population, sans condition d'équipement,
        de débit, de vue ou de maîtrise du vocabulaire administratif. Les exigences de
        cette page ne sont pas des recommandations&nbsp;: un service qui ne les respecte pas
        exclut une partie de ses usagers.
      </p>
    </div>

    <!-- ===================================================== -->
    <section class="doc-section" id="criteres">
      <h2>Critères de conformité</h2>
      <p class="doc-section-intro">
        Le niveau visé est WCAG&nbsp;2.1&nbsp;AA. Les exigences marquées
        <span class="fs-badge fs-badge--succes">Système</span> sont tenues par les
        composants tels qu'ils sont livrés, et vérifiées par la mesure décrite plus
        bas. Un projet qui les reprend sans les modifier part de là&nbsp;; il n'en
        hérite pas pour autant une conformité globale, qui dépend de son contenu, de
        sa structure et de ses parcours.
      </p>

      <div class="doc-bloc">
        <h3>Tableau de conformité</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Exigences appliquées par le système et points restant à la charge du projet</caption>
            <thead>
              <tr><th>Exigence</th><th>Valeur imposée</th><th>Portée</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Contraste du texte courant</td>
                <td class="fs-tabulaire">4,5&nbsp;: 1 minimum</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Contraste des grands titres</td>
                <td class="fs-tabulaire">3&nbsp;: 1 minimum</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Contraste des bordures de champ</td>
                <td class="fs-tabulaire">3&nbsp;: 1 minimum</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Taille du corps de texte</td>
                <td class="fs-tabulaire">14&nbsp;px, 12&nbsp;px pour la navigation (guide, §&nbsp;2.4.1.3)</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Cible tactile</td>
                <td class="fs-tabulaire">44 × 44&nbsp;px, deux exceptions</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Focus visible au clavier</td>
                <td>Anneau de 2&nbsp;px, halo sur toute surface</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Agrandissement du texte</td>
                <td class="fs-tabulaire">200&nbsp;% sans perte de contenu</td>
                <td><span class="fs-badge fs-badge--succes">Système</span></td>
              </tr>
              <tr>
                <td>Langue du document</td>
                <td><code>lang</code> déclaré et exact</td>
                <td><span class="fs-badge fs-badge--alerte">Projet</span></td>
              </tr>
              <tr>
                <td>Texte de remplacement des images</td>
                <td>Descriptif ou vide si décoratif</td>
                <td><span class="fs-badge fs-badge--alerte">Projet</span></td>
              </tr>
              <tr>
                <td>Hiérarchie des titres</td>
                <td>Un seul <code>h1</code>, aucun niveau sauté</td>
                <td><span class="fs-badge fs-badge--alerte">Projet</span></td>
              </tr>
              <tr>
                <td>Libellé de lien explicite hors contexte</td>
                <td>Pas de «&nbsp;cliquez ici&nbsp;»</td>
                <td><span class="fs-badge fs-badge--alerte">Projet</span></td>
              </tr>
              <tr>
                <td>Sous-titres des contenus vidéo</td>
                <td>Systématiques</td>
                <td><span class="fs-badge fs-badge--alerte">Projet</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Les deux exceptions à la cible de 44&nbsp;px</h3>
        <p class="doc-bloc-note">
          La règle des 44&nbsp;px est celle de la charte, plus exigeante que le
          niveau visé&nbsp;: elle reprend le critère 2.5.5 de WCAG&nbsp;2.1, de
          niveau&nbsp;AAA. Pour mémoire, WCAG&nbsp;2.2, que la charte ne revendique pas
          encore, fixe le plancher&nbsp;AA à 24&nbsp;×&nbsp;24&nbsp;px (critère 2.5.8). Les deux cas ci-dessous restent très au-dessus de ce
          plancher, et sont documentés ici plutôt que passés sous silence.
        </p>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Cas</th><th>Dessin</th><th>Cible effective</th><th>Pourquoi</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Bouton à icône en taille réduite</td>
                <td class="fs-tabulaire">36 × 36&nbsp;px</td>
                <td class="fs-tabulaire">44 × 44&nbsp;px</td>
                <td>Un calque transparent déborde de 4&nbsp;px. La zone qui répond
                    atteint la cible sans épaissir les barres d'outils denses.</td>
              </tr>
              <tr>
                <td>Jour de calendrier sous 400&nbsp;px de large</td>
                <td class="fs-tabulaire">38 à 44 × 40&nbsp;px</td>
                <td class="fs-tabulaire">38 à 44 × 40&nbsp;px</td>
                <td>Sept cases de 44&nbsp;px débordent d'un téléphone étroit. Au-delà de
                    400&nbsp;px, la case fait bien 44 × 44&nbsp;px.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Portée de la vérification</h3>
        <p class="doc-bloc-note">
          Ce qui a été mesuré, comment, et ce qui ne l'a pas encore été. Le tableau
          de conformité ne vaut que dans ces limites.
        </p>

        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">Mesuré</p>
            <ul>
              <li><strong>130 couples de couleurs</strong> effectivement employés par
                  <code>faso.css</code> (texte sur fond, variante de bouton, état
                  sémantique, contour de contrôle), calculés dans les deux thèmes,
                  transparences composées sur leur fond réel. Aucun sous le seuil.
                  Le décompte vient de <code>tests/contraste.js</code>, rejoué à
                  chaque <code>npm test</code>.</li>
              <li><strong>Les dimensions des cibles tactiles</strong>, relevées dans la
                  feuille de styles et reportées ci-dessus sans arrondi favorable.</li>
              <li><strong>Le balisage des huit pages</strong>&nbsp;: bonne formation,
                  aucune classe orpheline.</li>
              <li><strong>Le comportement au clavier</strong> des onglets, du menu, de la
                  modale, du panneau et de l'autocomplétion, par lecture du code et
                  essais automatisés sur un document simulé.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">Non mesuré à ce jour</p>
            <ul>
              <li><strong>Aucun essai avec un lecteur d'écran réel</strong>&nbsp;: NVDA,
                  JAWS et VoiceOver restent à passer.</li>
              <li><strong>Aucun audit RGAA complet</strong> par un tiers, ni grille de
                  critères renseignée.</li>
              <li><strong>Aucun parcours complet dans un navigateur</strong>&nbsp;: les
                  essais s'appuient sur un document simulé, qui ne remplace pas le
                  rendu réel.</li>
              <li><strong>Aucun test avec des personnes en situation de handicap</strong>,
                  la seule vérification réellement décisive.</li>
            </ul>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          Tant que ces quatre points ne sont pas levés, la formule juste est
          <strong>&laquo;&nbsp;conçu pour WCAG&nbsp;2.1&nbsp;AA, vérifié par la mesure sur les
          points listés &raquo;</strong>, et non &laquo;&nbsp;conforme&nbsp;&raquo;. La
          déclaration d'accessibilité d'un service ne peut être établie qu'après
          l'audit, service par service.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Vérification des contrastes</h3>
        <p class="doc-bloc-note">
          Les rapports ci-dessous sont calculés dans le navigateur à partir de la
          palette réelle, selon la formule WCAG 2.1. Ils se mettent à jour si la
          palette change.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Association</th><th>Emploi</th><th>Rapport</th></tr>
            </thead>
            <tbody>
              <tr><td>Texte 900 sur blanc</td><td>Texte courant</td><td><span data-contraste="#211d1a/#ffffff"></span></td></tr>
              <tr><td>Texte 600 sur blanc</td><td>Texte atténué, aide</td><td><span data-contraste="#635c55/#ffffff"></span></td></tr>
              <tr><td>Texte 550 sur blanc</td><td>Légende, mention secondaire</td><td><span data-contraste="#756d66/#ffffff"></span></td></tr>
              <tr><td>Texte 550 sur surface enfoncée</td><td>Légende dans un encadré</td><td><span data-contraste="#756d66/#f7f5f2"></span></td></tr>
              <tr><td>Bordure 450 sur blanc</td><td>Contour d'un champ, seuil de 3:1</td><td><span data-contraste="#8e867f/#ffffff"></span></td></tr>
              <tr><td>Bordure 450 sur surface enfoncée</td><td>Contour d'un champ désactivé</td><td><span data-contraste="#8e867f/#f7f5f2"></span></td></tr>
              <tr><td>Blanc sur vert 600</td><td>Bouton principal</td><td><span data-contraste="#ffffff/#007d3c"></span></td></tr>
              <tr><td>Blanc sur rouge 600</td><td>Bouton de suppression</td><td><span data-contraste="#ffffff/#d41f1f"></span></td></tr>
              <tr><td>Blanc sur vert 500</td><td>Réservé aux marques nationales, jamais sous un texte</td><td><span data-contraste="#ffffff/#009e49"></span></td></tr>
              <tr><td>Blanc sur rouge 500</td><td>Réservé aux marques nationales, jamais sous un texte</td><td><span data-contraste="#ffffff/#ef2b2d"></span></td></tr>
              <tr><td>Noir sur or 300</td><td>Bouton de mise en avant</td><td><span data-contraste="#211d1a/#fcd116"></span></td></tr>
              <tr><td>Blanc sur or 300</td><td>Association interdite</td><td><span data-contraste="#ffffff/#fcd116"></span></td></tr>
              <tr><td>Vert 700 sur blanc</td><td>Lien dans le texte</td><td><span data-contraste="#045527/#ffffff"></span></td></tr>
              <tr><td>Rouge 700 sur rouge 50</td><td>Message d'erreur</td><td><span data-contraste="#b01616/#fff1f1"></span></td></tr>
              <tr><td>Ocre 700 sur ocre 50</td><td>Message d'avertissement</td><td><span data-contraste="#7d5500/#fdf4e3"></span></td></tr>
            </tbody>
          </table>
        </div>

        <div class="doc-note fs-mt-6">
          L'association du blanc sur l'or du drapeau est explicitement interdite&nbsp;:
          son rapport est très inférieur au seuil. C'est pour cela que le rôle
          <code>--relief-texte</code> impose un texte sombre. Le rouge et le vert
          officiels du drapeau (palier 500) n'atteignent pas non plus 4,5&nbsp;: 1 sous
          un texte blanc. Ils restent aux marques nationales (drapeau, filet, repère,
          emblème)&nbsp;; boutons, liens et messages emploient le palier 600.
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="clavier">
      <h2>Navigation au clavier</h2>
      <p class="doc-section-intro">
        Toute action réalisable à la souris doit l'être au clavier, dans un ordre
        logique et avec un repère visible en permanence.
      </p>

      <div class="doc-bloc">
        <h3>Conventions appliquées</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Touche</th><th>Comportement</th><th>Composants</th></tr>
            </thead>
            <tbody>
              <tr><td><kbd>Tab</kbd></td><td>Passe à l'élément interactif suivant dans l'ordre du document</td><td>Tous</td></tr>
              <tr><td><kbd>Entrée</kbd></td><td>Active un lien ou un bouton</td><td>Tous</td></tr>
              <tr><td><kbd>Espace</kbd></td><td>Coche, décoche, ou active un bouton</td><td>Cases, interrupteurs, boutons</td></tr>
              <tr><td><kbd>Échap</kbd></td><td>Ferme et rend le focus au déclencheur</td><td>Modale, panneau latéral, menu</td></tr>
              <tr><td><kbd>&larr;</kbd> <kbd>&rarr;</kbd></td><td>Change d'onglet ou de bouton radio</td><td>Onglets, groupes radio</td></tr>
              <tr><td><kbd>Début</kbd> <kbd>Fin</kbd></td><td>Atteint le premier ou le dernier onglet</td><td>Onglets</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Piège de focus</h3>
        <p class="doc-bloc-note">
          Lorsqu'une modale ou un panneau latéral est ouvert, le focus reste enfermé
          à l'intérieur. L'élément <code>&lt;dialog&gt;</code> l'assure nativement&nbsp;;
          le panneau latéral l'implémente explicitement dans <code>faso.js</code>.
          À la fermeture, le focus revient sur l'élément qui a déclenché l'ouverture,
          faute de quoi l'usager au clavier est renvoyé en début de document.
        </p>

        <div class="doc-demo" data-libelle="Essai au clavier">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--tertiaire" data-ouvre-modale="modale-clavier">
              Ouvrir, puis parcourir au clavier
            </button>

            <dialog class="fs-modale" id="modale-clavier" aria-labelledby="modale-clavier-titre">
              <div class="fs-modale-entete">
                <h3 class="fs-modale-titre" id="modale-clavier-titre">Piège de focus</h3>
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-modale aria-label="Fermer"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
              </div>
              <div class="fs-modale-corps">
                <p>Appuyez sur la touche de tabulation à plusieurs reprises. Le focus tourne à l'intérieur de cette fenêtre sans en sortir.</p>
                <p>Appuyez ensuite sur Échap. La fenêtre se ferme et le focus revient sur le bouton d'origine.</p>
              </div>
              <div class="fs-modale-pied">
                <button type="button" class="fs-btn fs-btn--tertiaire" data-ferme-modale>Fermer</button>
                <button type="button" class="fs-btn fs-btn--principal" data-ferme-modale>OK</button>
              </div>
            </dialog>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="reseau">
      <h2>Contrainte de réseau et d'équipement</h2>
      <p class="doc-section-intro">
        L'usager type consulte un service public depuis un téléphone d'entrée de
        gamme, sur un réseau instable, avec un forfait facturé au volume. Ce n'est
        pas un cas limite, c'est le cas nominal.
      </p>

      <div class="doc-bloc">
        <h3>Conséquences de conception</h3>
        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">Imposé</p>
            <ul>
              <li>Le contenu essentiel s'affiche sans JavaScript. L'accordéon, les tableaux et les formulaires restent utilisables si le script échoue.</li>
              <li>Les liens de téléchargement annoncent format et poids avant le clic.</li>
              <li>Les images portent des dimensions explicites pour éviter les décalages de mise en page au chargement.</li>
              <li>La progression d'un formulaire long est sauvegardée côté serveur à chaque étape.</li>
              <li>Une interruption de réseau est signalée et les données saisies sont conservées.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">Proscrit</p>
            <ul>
              <li>Police de caractères bloquant l'affichage du texte pendant son chargement.</li>
              <li>Image décorative de plus de 200&nbsp;Ko, carrousel automatique, vidéo en lecture automatique.</li>
              <li>Rendu du contenu entièrement dépendant d'un script côté client.</li>
              <li>Rechargement complet de la page à chaque saisie de champ.</li>
              <li>Police de caractères chargée depuis un domaine tiers sans solution de repli locale.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="budget">
      <h2>Budget de performance</h2>
      <p class="doc-section-intro">
        Un principe sans chiffre ne se vérifie pas. Les plafonds ci-dessous
        s'appliquent au <strong>poids transféré</strong>, compression comprise, car
        c'est lui que l'usager paie sur un forfait facturé au volume. Ils sont
        contrôlés à la recette, au même titre que l'accessibilité.
      </p>

      <div class="doc-bloc">
        <h3>Plafonds</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Valeurs mesurées sur une page vidée du cache, en transfert réseau</caption>
            <thead>
              <tr><th>Grandeur</th><th>Objectif</th><th>Plafond</th><th>Motif</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Page complète</td>
                <td class="fs-tabulaire">250 Ko</td>
                <td class="fs-tabulaire">500 Ko</td>
                <td>Au-delà, le coût en données devient perceptible sur un forfait prépayé.</td>
              </tr>
              <tr>
                <td>Première page utile</td>
                <td class="fs-tabulaire">100 Ko</td>
                <td class="fs-tabulaire">150 Ko</td>
                <td>Ce qu'il faut pour afficher le contenu principal, hors images en contrebas.</td>
              </tr>
              <tr>
                <td>Affichage du contenu principal</td>
                <td class="fs-tabulaire">2 s</td>
                <td class="fs-tabulaire">3 s</td>
                <td>Mesuré en 3G lente, sur un appareil d'entrée de gamme.</td>
              </tr>
              <tr>
                <td>JavaScript</td>
                <td class="fs-tabulaire">30 Ko</td>
                <td class="fs-tabulaire">100 Ko</td>
                <td>Le temps d'analyse pèse davantage que le téléchargement sur un processeur modeste.</td>
              </tr>
              <tr>
                <td>Feuilles de style</td>
                <td class="fs-tabulaire">30 Ko</td>
                <td class="fs-tabulaire">60 Ko</td>
                <td>Le CSS bloque l'affichage&nbsp;: il est toujours sur le chemin critique.</td>
              </tr>
              <tr>
                <td>Polices</td>
                <td class="fs-tabulaire">60 Ko</td>
                <td class="fs-tabulaire">100 Ko</td>
                <td>Deux familles au plus, hébergées localement, <code>font-display: swap</code> obligatoire. Le système s'établit à 79 Ko (Montserrat 37 Ko, Roboto 42 Ko).</td>
              </tr>
              <tr>
                <td>Image, à l'unité</td>
                <td class="fs-tabulaire">100 Ko</td>
                <td class="fs-tabulaire">200 Ko</td>
                <td>Dimensionnée à sa taille d'affichage, jamais au-delà.</td>
              </tr>
              <tr>
                <td>Requêtes</td>
                <td class="fs-tabulaire">20</td>
                <td class="fs-tabulaire">40</td>
                <td>Sur réseau à forte latence, chaque requête coûte plus que son contenu.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Où se situe le système lui-même</h3>
        <p class="doc-bloc-note">
          Le budget s'applique d'abord à la charte elle-même. Poids réel des
          fichiers livrés, mesuré sur cette version&nbsp;:
        </p>

        <div class="fs-graphe">
          <p class="fs-graphe-titre">Poids transféré du socle, compression comprise</p>
          <p class="fs-graphe-note">Ces fichiers sont mis en cache&nbsp;: ils ne sont téléchargés qu'à la première visite.</p>

          <div class="fs-barres" data-max="100">
            <div class="fs-barre" data-valeur="44.7">
              <span class="fs-barre-libelle">Emblème <code>armoiries.svg</code></span>
              <span class="fs-barre-valeur">44,7 Ko</span>
              <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
            </div>
            <div class="fs-barre fs-barre--s2" data-valeur="20.6">
              <span class="fs-barre-libelle">Composants <code>faso.css</code></span>
              <span class="fs-barre-valeur">20,6 Ko</span>
              <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
            </div>
            <div class="fs-barre fs-barre--s3" data-valeur="24">
              <span class="fs-barre-libelle">Comportements <code>faso.js</code></span>
              <span class="fs-barre-valeur">24,0 Ko</span>
              <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
            </div>
            <div class="fs-barre fs-barre--s4" data-valeur="6.8">
              <span class="fs-barre-libelle">Jetons <code>tokens.css</code></span>
              <span class="fs-barre-valeur">6,8 Ko</span>
              <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
            </div>
            <div class="fs-barre fs-barre--s5" data-valeur="2.9">
              <span class="fs-barre-libelle">Icônes <code>icones.css</code></span>
              <span class="fs-barre-valeur">2,9 Ko</span>
              <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
            </div>
            <div class="fs-barre fs-barre--s6" data-valeur="0.6">
              <span class="fs-barre-libelle">Amorce <code>faso-amorce.js</code></span>
              <span class="fs-barre-valeur">0,6 Ko</span>
              <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
            </div>
          </div>

          <details class="fs-graphe-donnees">
            <summary>Consulter les données sous forme de tableau</summary>
            <div class="fs-tableau-cadre">
              <table class="fs-tableau fs-tableau--compact">
                <thead>
                  <tr><th>Fichier</th><th class="fs-num">Poids transféré</th></tr>
                </thead>
                <tbody>
                  <tr><td>Emblème <code>armoiries.svg</code></td><td class="fs-num">44,7 Ko</td></tr>
                  <tr><td>Composants <code>faso.css</code></td><td class="fs-num">20,6 Ko</td></tr>
                  <tr><td>Comportements <code>faso.js</code></td><td class="fs-num">24,0 Ko</td></tr>
                  <tr><td>Jetons <code>tokens.css</code></td><td class="fs-num">6,8 Ko</td></tr>
                  <tr><td>Icônes <code>icones.css</code></td><td class="fs-num">2,9 Ko</td></tr>
                  <tr><td>Amorce <code>faso-amorce.js</code></td><td class="fs-num">0,6 Ko</td></tr>
                  <tr><td><strong>Total</strong></td><td class="fs-num"><strong>99,6 Ko</strong></td></tr>
                </tbody>
              </table>
            </div>
          </details>

          <p class="fs-legende fs-mt-6">
            Socle complet&nbsp;: <strong>99,6&nbsp;Ko</strong> transférés, auxquels s'ajoutent
            <strong>79&nbsp;Ko</strong> de polices, servies depuis le domaine du service.
            Avec le HTML d'une page, le total s'établit autour de
            <strong>185&nbsp;Ko</strong> à la première visite, sous l'objectif de 250&nbsp;Ko
            pour une page complète. Aux visites suivantes, les polices et le socle
            viennent du cache.
          </p>
        </div>

        <div class="doc-note fs-mt-6">
          L'emblème représente à lui seul près de la moitié du socle. C'est le prix d'un tracé
          vectoriel fidèle. Il reste vingt fois plus léger que le raster dont il est
          issu, et il n'est téléchargé qu'une fois pour l'ensemble du service.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Règles d'application</h3>
        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">Imposé</p>
            <ul>
              <li>Héberger les polices sur le domaine du service&nbsp;: un tiers ajoute une résolution DNS, une connexion et une dépendance hors de contrôle.</li>
              <li>Déclarer <code>width</code> et <code>height</code> sur toute image, pour éviter le décalage de mise en page au chargement.</li>
              <li>Charger en différé les images situées sous la ligne de flottaison.</li>
              <li>Servir les fichiers compressés et avec une durée de cache longue.</li>
              <li>Mesurer sur un appareil réel, pas sur un poste de développement.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">Proscrit</p>
            <ul>
              <li>Toute bibliothèque tierce pour un besoin couvert par le système.</li>
              <li>Vidéo en lecture automatique, carrousel, animation permanente.</li>
              <li>Mesure d'audience chargée avant le contenu.</li>
              <li>Police de caractères bloquant l'affichage du texte pendant son chargement.</li>
              <li>Image servie à une résolution supérieure à sa taille d'affichage.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="redaction">
      <h2>Ton administratif et rédaction</h2>
      <p class="doc-section-intro">
        La formulation fait partie de l'interface. Un intitulé ambigu produit
        les mêmes abandons qu'un bouton mal placé.
      </p>

      <div class="doc-bloc">
        <h3>Principes de rédaction</h3>
        <ul class="fs-liste-puces fs-mesure">
          <li>Employer un vocabulaire courant et expliciter les termes administratifs à leur première occurrence.</li>
          <li>S'adresser à l'usager à la deuxième personne du pluriel, de manière neutre.</li>
          <li>Préférer la voix active&nbsp;: «&nbsp;Déposez votre demande&nbsp;» plutôt que «&nbsp;La demande doit être déposée&nbsp;».</li>
          <li>Indiquer le coût, le délai, les pièces et l'administration responsable avant tout formulaire.</li>
          <li>Proscrire les formulations promotionnelles dans les parcours de démarche.</li>
          <li>Donner une échéance datée plutôt qu'une durée relative lorsqu'une action est attendue.</li>
        </ul>
      </div>

      <div class="doc-bloc" id="vocabulaire">
        <h3>Vocabulaire et libellés</h3>
        <p class="doc-bloc-note">
          Règles du guide pour tous les textes d'écran&nbsp;: libellés, titres de
          colonne, en-têtes de groupe, messages (guide, §&nbsp;4.4.4).
        </p>
        <ul class="fs-liste-puces fs-mesure">
          <li>Employer le vocabulaire du domaine de l'usager, pas celui de l'administration qui instruit&nbsp;: «&nbsp;acte de naissance&nbsp;» plutôt que l'intitulé du registre.</li>
          <li>Choisir des termes explicites et sans ambiguïté, en phrases simples, à la forme affirmative&nbsp;: «&nbsp;Le fichier dépasse 5&nbsp;Mo&nbsp;» plutôt que «&nbsp;Le fichier n'est pas en dessous de la taille permise&nbsp;».</li>
          <li>Indiquer l'unité de mesure, en abrégé si elle est connue de tous&nbsp;: <code>Montant (F CFA)</code>, <code>Surface (m²)</code>, <code>Poids maximal&nbsp;: 5&nbsp;Mo</code>.</li>
          <li>Éviter les abréviations. Si la place manque, ne retenir que les plus répandues&nbsp;; pour un mot court, préférer la contraction (<code>Nbre</code> pour nombre), pour un mot long, la troncature (<code>Administr.</code> pour administration).</li>
          <li>Ne pas employer de synonymes&nbsp;: un même objet ou une même action porte partout le même libellé. Si l'écran dit «&nbsp;demande&nbsp;», le courriel ne dit pas «&nbsp;dossier&nbsp;» et le bouton ne dit pas «&nbsp;requête&nbsp;».</li>
          <li>Dans une liste, un libellé trop long pour sa ligne se tronque au milieu, avec des points de suspension, pour garder le début et la fin&nbsp;: <code>Certificat de … du casier judiciaire</code>. Quand seul le début discrimine les éléments, couper à la fin. La propriété CSS <code>text-overflow: ellipsis</code> ne coupe qu'à la fin&nbsp;: la troncature au milieu se prépare côté serveur, et le libellé complet reste disponible pour les lecteurs d'écran.</li>
        </ul>
      </div>

      <div class="doc-bloc">
        <h3>Messages d'erreur</h3>
        <p class="doc-bloc-note">
          Un message d'erreur indique la correction attendue. Constater l'erreur sans
          dire quoi faire laisse l'usager bloqué. Le ton reste concis et jamais
          vexatoire&nbsp;: aucun message ne contient «&nbsp;faute&nbsp;», «&nbsp;mauvaise
          manipulation&nbsp;» ni «&nbsp;illégal&nbsp;» (guide, §&nbsp;7.1.1, point&nbsp;7).
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>À proscrire</th><th>À écrire</th></tr>
            </thead>
            <tbody>
              <tr>
                <td class="fs-attenue">Champ invalide</td>
                <td>Ajoutez le symbole arobase et le nom de domaine, par exemple nom@exemple.bf</td>
              </tr>
              <tr>
                <td class="fs-attenue">Erreur de format</td>
                <td>Le numéro CNIB comporte une lettre suivie de sept chiffres, sans espace</td>
              </tr>
              <tr>
                <td class="fs-attenue">Fichier refusé</td>
                <td>Ce fichier pèse 8,4&nbsp;Mo. Réduisez sa taille à 5&nbsp;Mo au maximum, ou déposez une photographie plutôt qu'un scan</td>
              </tr>
              <tr>
                <td class="fs-attenue">Une erreur est survenue</td>
                <td>La demande n'a pas pu être transmise. Vos données sont conservées. Réessayez dans quelques instants</td>
              </tr>
              <tr>
                <td class="fs-attenue">Session expirée</td>
                <td>Votre session a expiré après trente minutes d'inactivité. Votre brouillon a été enregistré&nbsp;: identifiez-vous pour le retrouver</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc" id="messages-standard">
        <h3>Messages standardisés</h3>
        <p class="doc-bloc-note">
          Le guide fixe la formulation des situations les plus courantes (guide,
          §&nbsp;8.1.2). Les textes ci-dessous la reprennent, mise en typographie
          française&nbsp;; les mentions entre crochets se remplacent par la valeur réelle.
          Partout où le message accompagne un champ, il s'affiche sous ce champ, sans
          bouton à acquitter&nbsp;: le bouton du guide ne sert que dans une boîte de
          message.
        </p>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Situations et messages standardisés</caption>
            <thead>
              <tr><th>Situation</th><th>Message</th><th>Boutons</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Recherche multicritère&nbsp;: trop de réponses pour les afficher</td>
                <td>Trop de [éléments] ([nombre]) correspondent à votre sélection. Veuillez modifier vos critères de sélection.</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Recherche multicritère&nbsp;: aucune réponse</td>
                <td>Aucun [élément] ne correspond à votre sélection. Veuillez modifier vos critères de sélection.</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Champ de longueur fixe mal rempli</td>
                <td>Le [nom du champ] doit comporter au moins (ou au plus) [nombre] caractères [alphabétiques ou numériques]. Le format attendu est donné si nécessaire.</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Code inconnu</td>
                <td>La valeur «&nbsp;[valeur saisie]&nbsp;» n'est pas une valeur de [catégorie].</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Champ obligatoire laissé vide</td>
                <td>La saisie du [nom du champ] est obligatoire.</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Date au mauvais format</td>
                <td>La date doit être saisie sous le format «&nbsp;jj/mm/aaaa&nbsp;».</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Nombre ou date hors des bornes</td>
                <td>Le [nom du champ] doit être compris entre [minimum] et [maximum].</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Nombre ou date au-delà ou en deçà d'un seuil</td>
                <td>Le [nom du champ] doit être inférieur (ou supérieur) à [seuil]. La date doit être antérieure (ou postérieure) au [date].</td>
                <td>OK</td>
              </tr>
              <tr>
                <td>Action à risque à confirmer</td>
                <td>Souhaitez-vous vraiment [action]&nbsp;?</td>
                <td>Continuer, Arrêter</td>
              </tr>
              <tr>
                <td>Traitement en tâche de fond terminé</td>
                <td>Le [nom de l'opération] est terminé.</td>
                <td>OK</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p class="fs-legende fs-mt-4">
          Ces textes sont repris mot pour mot du guide, «&nbsp;Veuillez&nbsp;» compris&nbsp;: ils
          sont faits pour être identiques d'une application à l'autre, et c'est la seule
          exception à la règle de l'impératif direct énoncée plus haut. Pour une erreur de saisie, le texte du tableau «&nbsp;Messages d'erreur&nbsp;» va
          plus loin que le message standard&nbsp;: il donne aussi la correction.
        </p>
      </div>

      <div class="doc-bloc" id="boites-message">
        <h3>Boutons des boîtes de message</h3>
        <p class="doc-bloc-note">
          Chaque type de message a ses boutons (guide, §&nbsp;7.1.2 à 7.1.5). Une boîte
          de message porte un titre qui indique son origine, une icône à gauche du
          texte et, s'il y en a un, l'identifiant du message en bas à droite
          (guide, §&nbsp;7.1.1).
        </p>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Type</th><th>Emploi</th><th>Boutons, action par défaut en premier</th></tr>
            </thead>
            <tbody>
              <tr><td>Information</td><td>Résultat d'une action sans risque</td><td>OK</td></tr>
              <tr><td>Avertissement</td><td>Action qui peut s'avérer dangereuse&nbsp;; fenêtre modale si les conséquences sont graves</td><td>Annuler, Continuer, Aide</td></tr>
              <tr><td>Confirmation</td><td>Obligatoire avant toute opération irréversible&nbsp;; toujours une question, toujours modale</td><td>Continuer, Arrêter (ou Annuler)</td></tr>
              <tr><td>Blocage</td><td>Action irréalisable dans le contexte</td><td>OK</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Intitulés d'action</h3>
        <p class="doc-bloc-note">
          Un bouton décrit ce qu'il déclenche. Un usager qui ne lit que les boutons
          doit comprendre la conséquence de son geste. Le guide déconseille les
          questions auxquelles on ne répond que par «&nbsp;Oui&nbsp;» ou «&nbsp;Non&nbsp;»
          et préfère des verbes d'action (guide, §&nbsp;7.1.1, point&nbsp;3).
        </p>

        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À écrire</p>
            <ul>
              <li>Déposer la demande</li>
              <li>Transmettre la pièce justificative</li>
              <li>Supprimer définitivement le brouillon</li>
              <li>Télécharger le récépissé au format PDF</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Valider</li>
              <li>Oui</li>
              <li>Continuer, sur un bouton de formulaire ou d'étape</li>
              <li>Cliquez ici</li>
            </ul>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          <strong>«&nbsp;Continuer&nbsp;» et «&nbsp;Arrêter&nbsp;» ont leur place dans les
          boîtes de confirmation et d'avertissement</strong>, où le guide les prévoit
          (guide, §&nbsp;7.1.3 et 7.1.4)&nbsp;: la question posée par la boîte nomme
          l'action, le bouton y répond. Partout ailleurs, sur un formulaire ou entre
          deux étapes, le bouton nomme ce qu'il déclenche&nbsp;: «&nbsp;Passer au
          paiement&nbsp;», pas «&nbsp;Continuer&nbsp;».
        </div>
      </div>

      <div class="doc-bloc" id="temps-reponse">
        <h3>Temps de réponse</h3>
        <p class="doc-bloc-note">
          Ce que l'usager perçoit selon la durée d'attente, et ce que l'interface lui
          doit (guide, §&nbsp;7.2). Sur un réseau lent, les seuils sont franchis plus
          souvent qu'on ne le croit.
        </p>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Seuil</th><th>Au-delà, l'usager…</th><th>Réponse de l'interface</th></tr>
            </thead>
            <tbody>
              <tr><td class="fs-tabulaire">0,1&nbsp;s</td><td>ne perçoit plus la réaction comme instantanée</td><td>Retour visuel immédiat sur l'élément actionné (état du bouton)</td></tr>
              <tr><td class="fs-tabulaire">1&nbsp;s</td><td>a l'impression de ne plus contrôler le système</td><td>Pointeur d'attente&nbsp;: <code>aria-busy="true"</code> sur la zone en cours de traitement, ou sur <code>&lt;body&gt;</code> si plus rien n'est utilisable (guide, §&nbsp;7.1.6)</td></tr>
              <tr><td class="fs-tabulaire">6&nbsp;s</td><td>a besoin de savoir où en est le traitement</td><td>Indicateur de progression <code>.fs-jauge</code>, avec le pourcentage ou le temps écoulé en texte</td></tr>
              <tr><td class="fs-tabulaire">10&nbsp;s</td><td>n'est plus attentif à l'échange en cours</td><td>Indicateur de progression obligatoire, raison de l'attente affichée, message à la fin du traitement</td></tr>
            </tbody>
          </table>
        </div>
        <ul class="fs-liste-puces fs-mesure fs-mt-6">
          <li>Dire pourquoi l'usager attend&nbsp;: «&nbsp;Vérification de vos pièces auprès du registre&nbsp;» se supporte mieux qu'une roue qui tourne.</li>
          <li>Si l'usager peut continuer à travailler, l'indicateur reste dans la page&nbsp;; s'il ne le peut pas, il s'affiche dans une fenêtre modale.</li>
          <li>Un bouton «&nbsp;Annuler&nbsp;» ou «&nbsp;Arrêter&nbsp;» interrompt réellement le traitement et rend la main. Un bouton qui ne fait que masquer l'indicateur est proscrit.</li>
        </ul>
      </div>

      <div class="doc-bloc" id="confidentialite">
        <h3>Champs confidentiels</h3>
        <p class="doc-bloc-note">
          Les caractères saisis dans un champ confidentiel sont masqués (guide,
          §&nbsp;7.3)&nbsp;: mot de passe, code secret de paiement. Le masquage s'obtient par <code>type="password"</code>&nbsp;; il
          ne dispense ni du libellé ni de l'indication du format attendu.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Écriture des données</h3>
        <dl class="fs-definitions">
          <div><dt>Montants</dt><dd>Séparateur de milliers par espace insécable, unité explicite&nbsp;: <code>50 000 F CFA</code></dd></div>
          <div><dt>Dates</dt><dd>En toutes lettres dans le texte, en chiffres dans les tableaux&nbsp;: <code>18 septembre 2026</code> ou <code>18/09/2026</code></dd></div>
          <div><dt>Heures</dt><dd>Format 24 heures avec espace&nbsp;: <code>7 h 30</code></dd></div>
          <div><dt>Références de dossier</dt><dd>En caractères à chasse fixe, non traduites&nbsp;: <code>BF-2026-004871-OUA</code></dd></div>
          <div><dt>Téléphones</dt><dd>Indicatif séparé, groupes de deux chiffres&nbsp;: <code>+226 70 00 00 00</code></dd></div>
        </dl>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="declaration">
      <h2>Déclaration d'accessibilité</h2>
      <p class="doc-section-intro">
        Chaque service publie une déclaration d'accessibilité, liée depuis le pied
        de page de toutes ses pages.
      </p>

      <div class="doc-bloc">
        <h3>Contenu obligatoire</h3>
        <ul class="fs-liste-controle fs-mesure">
          <li>Le niveau de conformité constaté et la date du dernier audit.</li>
          <li>La liste des contenus non conformes, avec la justification et l'échéance de correction.</li>
          <li>Les moyens de signaler un défaut d'accessibilité et le délai de réponse.</li>
          <li>La voie de recours ouverte à l'usager en cas d'absence de réponse.</li>
          <li>Une solution de remplacement pour tout contenu non accessible, notamment un canal physique ou téléphonique.</li>
        </ul>

        <div class="fs-encart fs-encart--info fs-mt-8">
          <div class="fs-encart-corps">
            <p class="fs-encart-titre">Recette avant mise en production</p>
            <p>
              Tout service est vérifié au clavier seul, avec un lecteur d'écran, à
              200&nbsp;% d'agrandissement, sur un écran de 320&nbsp;px de large, et avec
              JavaScript désactivé. Un défaut constaté sur l'un de ces cinq points
              est bloquant.
            </p>
          </div>
        </div>
      </div>
    </section>

@endsection
