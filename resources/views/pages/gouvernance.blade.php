@extends('layouts.documentation')

@section('contenu')

    <div class="doc-entete">
      <nav class="doc-fil" aria-label="Fil d'Ariane">
        <a href="{{ page('index') }}">Charte</a> <span aria-hidden="true">/</span> <span>Gouvernance</span>
      </nav>
      <h1>Gouvernance</h1>
      <p class="fs-chapeau">
        Un système de conception qui n'a pas de propriétaire se fragmente&nbsp;; un système
        qui n'a pas de procédure se fige. Cette page fixe qui décide, comment le
        système évolue, et ce qu'un projet doit faire lorsque la charte ne prévoit pas
        son cas.
      </p>
    </div>

    <div class="fs-encart fs-encart--alerte" style="margin-bottom: var(--espace-12);">
      <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">Désignations à arrêter</p>
        <p>
          Les rôles et les procédures décrits ici sont des propositions de conception.
          Les organismes qui les exercent (autorité de tutelle, équipe du système,
          comité d'arbitrage), doivent être désignés par acte administratif avant
          l'entrée en vigueur de la charte. Les mentions <code>[à désigner]</code>
          marquent les points qui relèvent de cette décision et non de la conception.
        </p>
      </div>
    </div>

    <!-- ===================================================== -->
    <section class="doc-section" id="roles">
      <h2>Rôles et responsabilités</h2>
      <p class="doc-section-intro">
        Trois niveaux, et un principe. La décision revient au plus proche du
        terrain qui peut la prendre sans compromettre l'unité du système.
      </p>

      <div class="doc-bloc">
        <h3>Les trois niveaux</h3>

        <div class="fs-auto-grille" style="--min-colonne: 280px;">
          <article class="fs-carte">
            <div class="fs-carte-corps">
              <span class="fs-badge fs-badge--info" style="align-self:flex-start;">Niveau 1</span>
              <h4 class="fs-carte-titre" style="font-size: var(--taille-md);">Autorité de tutelle</h4>
              <p class="fs-carte-texte">
                Approuve les versions majeures, arbitre les désaccords persistants,
                engage la charte vis-à-vis des administrations. N'intervient pas dans
                les décisions courantes.
              </p>
              <p class="fs-legende"><code>[à désigner]</code></p>
            </div>
          </article>

          <article class="fs-carte">
            <div class="fs-carte-corps">
              <span class="fs-badge fs-badge--succes" style="align-self:flex-start;">Niveau 2</span>
              <h4 class="fs-carte-titre" style="font-size: var(--taille-md);">Équipe du système</h4>
              <p class="fs-carte-texte">
                Maintient les fichiers et la documentation, instruit les propositions,
                publie les versions mineures et correctives, tient le registre des
                dérogations, accompagne les projets.
              </p>
              <p class="fs-legende"><code>[à désigner]</code></p>
            </div>
          </article>

          <article class="fs-carte">
            <div class="fs-carte-corps">
              <span class="fs-badge fs-badge--neutre" style="align-self:flex-start;">Niveau 3</span>
              <h4 class="fs-carte-titre" style="font-size: var(--taille-md);">Référents de projet</h4>
              <p class="fs-carte-texte">
                Un référent par service numérique. Applique la charte, signale les
                manques, soumet les dérogations, atteste la conformité avant mise en
                production.
              </p>
              <p class="fs-legende">Désigné par l'administration porteuse du service</p>
            </div>
          </article>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Qui décide quoi</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Répartition des décisions</caption>
            <thead>
              <tr>
                <th>Décision</th>
                <th>Instruite par</th>
                <th>Arrêtée par</th>
                <th>Délai visé</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Correction d'un défaut</td>
                <td>Équipe du système</td>
                <td>Équipe du système</td>
                <td class="fs-tabulaire">10 jours ouvrés</td>
              </tr>
              <tr>
                <td>Nouveau composant ou variante</td>
                <td>Équipe du système</td>
                <td>Équipe du système</td>
                <td class="fs-tabulaire">30 jours ouvrés</td>
              </tr>
              <tr>
                <td>Modification d'un jeton existant</td>
                <td>Équipe du système</td>
                <td>Comité d'arbitrage</td>
                <td class="fs-tabulaire">45 jours ouvrés</td>
              </tr>
              <tr>
                <td>Dérogation ponctuelle d'un projet</td>
                <td>Référent de projet</td>
                <td>Équipe du système</td>
                <td class="fs-tabulaire">10 jours ouvrés</td>
              </tr>
              <tr>
                <td>Emploi de l'emblème hors règles</td>
                <td>Équipe du système</td>
                <td>Autorité de tutelle</td>
                <td class="fs-tabulaire">Sans délai garanti</td>
              </tr>
              <tr>
                <td>Version majeure</td>
                <td>Équipe du système</td>
                <td>Autorité de tutelle</td>
                <td class="fs-tabulaire">Annuel au plus</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="versionnement">
      <h2>Versionnement</h2>
      <p class="doc-section-intro">
        Le numéro de version se lit <code>majeure.mineure.corrective</code>. Ce qui
        détermine le rang n'est pas l'ampleur du travail mais l'effort imposé aux
        projets qui ont déjà intégré la charte.
      </p>

      <div class="doc-bloc">
        <h3>Ce qui fait changer chaque rang</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Rang</th><th>Déclencheurs</th><th>Effet pour un projet</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Majeure</strong><br><span class="fs-legende">2.0 → 3.0</span></td>
                <td>
                  Suppression ou renommage d'un jeton, d'une classe ou d'un composant&nbsp;;
                  modification du balisage exigé par un composant&nbsp;; changement de
                  <em>sens</em> d'un jeton&nbsp;; nouvelle dépendance entre fichiers.
                </td>
                <td>Reprise du code nécessaire. Guide de migration fourni.</td>
              </tr>
              <tr>
                <td><strong>Mineure</strong><br><span class="fs-legende">2.0 → 2.1</span></td>
                <td>
                  Nouveau composant, nouvelle variante, nouveau jeton, nouvelle icône,
                  passage d'un composant en <em>déconseillé</em>.
                </td>
                <td>Aucune reprise. Mise à jour des fichiers suffisante.</td>
              </tr>
              <tr>
                <td><strong>Corrective</strong><br><span class="fs-legende">2.0 → 2.0.1</span></td>
                <td>
                  Correction d'un défaut, ajustement visuel sans changement de
                  balisage, correction de documentation.
                </td>
                <td>Aucune reprise.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-note fs-mt-6">
          <strong>La valeur d'un jeton n'est pas son contrat.</strong> Ajuster
          <code>--vert-600</code> de deux points de luminosité est une version
          corrective. En revanche, faire pointer <code>--action</code> vers une autre
          famille chromatique change le sens du jeton et constitue une rupture, même
          si aucune ligne de code de projet n'a besoin d'être touchée.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Rythme de publication</h3>
        <ul class="fs-liste-puces fs-mesure">
          <li>Les versions correctives sont publiées au fil de l'eau.</li>
          <li>Les versions mineures sont regroupées et publiées au plus une fois par trimestre, pour que les projets n'aient pas à suivre un flux continu.</li>
          <li>Une version majeure au plus par an, annoncée six mois à l'avance.</li>
          <li>Chaque publication est accompagnée d'une entrée au <a href="#journal">journal des versions</a>.</li>
        </ul>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="cycle">
      <h2>Cycle de vie d'un composant</h2>
      <p class="doc-section-intro">
        Un composant n'entre pas dans la charte parce qu'il est utile à un projet,
        mais parce qu'il répond à un besoin constaté dans plusieurs. Il en sort selon
        une procédure, jamais du jour au lendemain.
      </p>

      <div class="doc-bloc">
        <h3>Les quatre états</h3>

        <div class="doc-demo" data-libelle="États d'un composant">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <ol class="fs-etapes">
              <li class="fs-etape" data-etat="faite">
                <span class="fs-etape-num">État 1</span>
                <span class="fs-etape-titre">Proposé</span>
              </li>
              <li class="fs-etape" data-etat="faite">
                <span class="fs-etape-num">État 2</span>
                <span class="fs-etape-titre">Expérimental</span>
              </li>
              <li class="fs-etape" data-etat="courante">
                <span class="fs-etape-num">État 3</span>
                <span class="fs-etape-titre">Stable</span>
              </li>
              <li class="fs-etape">
                <span class="fs-etape-num">État 4</span>
                <span class="fs-etape-titre">Déconseillé, puis retiré</span>
              </li>
            </ol>
          </div>
        </div>

        <dl class="fs-definitions fs-mt-6">
          <div>
            <dt><span class="fs-badge fs-badge--neutre">Proposé</span></dt>
            <dd>
              Besoin exprimé et instruit. Le composant n'existe pas encore dans les
              fichiers. Aucun projet ne doit l'anticiper.
            </dd>
          </div>
          <div>
            <dt><span class="fs-badge fs-badge--alerte">Expérimental</span></dt>
            <dd>
              Publié mais susceptible d'évoluer sans version majeure. Utilisable par
              les projets volontaires, qui acceptent d'en suivre les modifications.
              Un composant ne reste pas expérimental plus de deux versions mineures.
            </dd>
          </div>
          <div>
            <dt><span class="fs-badge fs-badge--succes">Stable</span></dt>
            <dd>
              État par défaut de tout composant publié dans cette charte. Ne peut
              changer de contrat qu'à l'occasion d'une version majeure.
            </dd>
          </div>
          <div>
            <dt><span class="fs-badge fs-badge--danger">Déconseillé</span></dt>
            <dd>
              Signalé dans la documentation, avec le composant de remplacement et la
              version de retrait. Continue de fonctionner pendant au moins
              <strong>deux versions mineures et une version majeure</strong>, soit un
              an au minimum.
            </dd>
          </div>
        </dl>
      </div>

      <div class="doc-bloc">
        <h3>Critères d'acceptation</h3>
        <p class="doc-bloc-note">
          Un composant proposé n'est publié que si les sept conditions sont réunies.
          Une seule non satisfaite suffit à ajourner la proposition.
        </p>

        <ul class="fs-liste-controle fs-mesure">
          <li><strong>Besoin partagé.</strong> Le besoin est constaté dans au moins deux services de l'État, ou découle d'une obligation réglementaire.</li>
          <li><strong>Absence de doublon.</strong> Aucun composant existant ne couvre le cas, même au prix d'une variante.</li>
          <li><strong>Aucune valeur en dur.</strong> Le composant ne consomme que des jetons.</li>
          <li><strong>Accessible.</strong> Contrastes conformes, navigation au clavier, cible tactile de 44&nbsp;px, état visible sans recours à la seule couleur.</li>
          <li><strong>Dégradation maîtrisée.</strong> Le contenu essentiel reste lisible sans JavaScript et sur écran de 320&nbsp;px.</li>
          <li><strong>Documenté.</strong> Intitulé, usage, règles d'emploi, exemple, et au moins un cas à proscrire.</li>
          <li><strong>Imprimable.</strong> Le composant s'imprime correctement ou est explicitement exclu de l'impression.</li>
        </ul>
      </div>

      <div class="doc-bloc">
        <h3>Signalement dans la documentation</h3>
        <p class="doc-bloc-note">
          L'état d'un composant est porté par une étiquette placée à côté de son
          intitulé. L'absence d'étiquette signifie <em>stable</em>&nbsp;: c'est le cas de
          la quasi-totalité de la charte.
        </p>

        <div class="doc-demo" data-libelle="Étiquettes d'état">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <h4 style="margin:0;">
              Bouton <code>.fs-btn</code>
            </h4>
            <h4 style="margin:0;">
              Motif tissé <code>.fs-tissage</code>
              <span class="fs-badge fs-badge--alerte">Expérimental</span>
            </h4>
            <h4 style="margin:0;">
              Ancien filet <code>.fs-filet--bandes</code>
              <span class="fs-badge fs-badge--danger">Déconseillé — retrait en 3.0</span>
            </h4>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="contribuer">
      <h2>Proposer un changement</h2>
      <p class="doc-section-intro">
        Toute administration peut proposer. La procédure est volontairement courte&nbsp;:
        une proposition mal instruite coûte plus cher à traiter qu'un besoin mal
        formulé mais honnête.
      </p>

      <div class="doc-bloc">
        <h3>La procédure</h3>

        <ol class="fs-pieces">
          <li>
            <span class="fs-pieces-num">1</span>
            <div>
              <p class="fs-pieces-nom">Vérifier que le besoin n'est pas déjà couvert</p>
              <p class="fs-pieces-detail">
                Parcourir les composants de base, les composants métier et les gabarits.
                La majorité des propositions reçues portent sur un cas déjà traité sous
                un autre nom.
              </p>
            </div>
          </li>
          <li>
            <span class="fs-pieces-num">2</span>
            <div>
              <p class="fs-pieces-nom">Décrire le besoin, pas la solution</p>
              <p class="fs-pieces-detail">
                Indiquer la tâche que l'usager doit accomplir, le service concerné, et
                pourquoi les composants existants ne suffisent pas. Une maquette est
                utile mais ne remplace pas l'énoncé du besoin.
              </p>
            </div>
          </li>
          <li>
            <span class="fs-pieces-num">3</span>
            <div>
              <p class="fs-pieces-nom">Transmettre au référent de son administration</p>
              <p class="fs-pieces-detail">
                Le référent filtre, complète et transmet à l'équipe du système. Une
                proposition transmise directement est réorientée vers le référent.
              </p>
            </div>
          </li>
          <li>
            <span class="fs-pieces-num">4</span>
            <div>
              <p class="fs-pieces-nom">Instruction</p>
              <p class="fs-pieces-detail">
                L'équipe rapproche la proposition des autres besoins reçus, vérifie les
                sept critères d'acceptation et répond&nbsp;: retenue, ajournée avec les
                éléments manquants, ou écartée avec le motif.
              </p>
            </div>
          </li>
          <li>
            <span class="fs-pieces-num">5</span>
            <div>
              <p class="fs-pieces-nom">Publication</p>
              <p class="fs-pieces-detail">
                Le composant retenu est publié en état expérimental à la prochaine
                version mineure, puis stabilisé après retour des projets.
              </p>
            </div>
          </li>
        </ol>

        <div class="fs-encart fs-encart--info fs-mt-8">
          <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
          <div class="fs-encart-corps">
            <p class="fs-encart-titre">Signaler un défaut</p>
            <p>
              Un défaut d'accessibilité, de contraste ou de comportement ne suit pas
              cette procédure. Il est signalé directement à l'équipe du système et
              traité en version corrective sous dix jours ouvrés.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="derogations">
      <h2>Dérogations</h2>
      <p class="doc-section-intro">
        Une charte qui n'admet aucune exception est contournée en silence. Mieux vaut
        une dérogation instruite et inscrite au registre qu'un écart invisible.
      </p>

      <div class="doc-bloc">
        <h3>Ce qui peut faire l'objet d'une dérogation</h3>

        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">Recevable</p>
            <ul>
              <li>Composant absent du système et nécessaire à une obligation réglementaire.</li>
              <li>Adaptation d'un gabarit imposée par un texte ou par un système tiers imposé.</li>
              <li>Contrainte technique documentée d'une plateforme existante.</li>
              <li>Densité d'information supérieure dans un outil réservé aux agents.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">Irrecevable</p>
            <ul>
              <li>Toute atteinte aux règles d'emploi de l'emblème, du filet ou de la devise.</li>
              <li>Tout abaissement du niveau d'accessibilité.</li>
              <li>Suppression du bandeau d'identification officielle ou des mentions de confiance.</li>
              <li>Préférence esthétique d'une administration ou d'un prestataire.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Régime de la dérogation</h3>
        <dl class="fs-definitions">
          <div><dt>Forme</dt><dd>Écrite, motivée, portée par le référent de projet.</dd></div>
          <div><dt>Portée</dt><dd>Limitée à un service et à un écran identifié. Une dérogation ne vaut jamais pour une administration entière.</dd></div>
          <div><dt>Durée</dt><dd>Douze mois, renouvelable une fois. Au-delà, le besoin est requalifié en proposition de composant.</dd></div>
          <div><dt>Publicité</dt><dd>Inscrite au registre des dérogations, consultable par toutes les administrations.</dd></div>
          <div><dt>Effet</dt><dd>Le service reste déclaré conforme à la charte, la dérogation étant mentionnée dans sa déclaration.</dd></div>
        </dl>

        <div class="doc-note fs-mt-6">
          Une dérogation accordée deux fois pour le même motif par deux services
          différents vaut constat d'un manque du système. L'équipe est alors tenue
          d'ouvrir une proposition de composant, sans attendre qu'on la lui soumette.
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="stockage">
      <h2>Ce que la charte dépose sur l'appareil</h2>
      <p class="doc-section-intro">
        Un système qui impose le consentement aux services doit commencer par
        déclarer ce qu'il dépose lui-même. La charte écrit deux choses, et rien
        d'autre&nbsp;: aucun identifiant, aucune mesure, aucun appel vers un tiers.
      </p>

      <div class="fs-tableau-cadre">
        <table class="fs-tableau">
          <thead>
            <tr>
              <th>Ce qui est écrit</th><th>Où</th><th>Combien de temps</th>
              <th>Consentement</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><code>faso-theme</code><br><span class="fs-faible">« dark » ou « light »</span></td>
              <td>Stockage local</td>
              <td>Jusqu'à effacement par l'usager</td>
              <td><span class="fs-badge fs-badge--succes">Non requis</span></td>
            </tr>
            <tr>
              <td><code>faso-consentement</code><br><span class="fs-faible">la décision, finalité par finalité</span></td>
              <td>Cookie, <code>SameSite=Lax</code>, <code>Secure</code> en https</td>
              <td>Six mois, puis la question est reposée</td>
              <td><span class="fs-badge fs-badge--succes">Non requis</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="doc-note fs-mt-6">
        Ces deux écritures ne demandent pas de consentement, et la raison est la
        même dans les deux cas&nbsp;: elles ne servent qu'à exécuter ce que l'usager a
        demandé. Le thème est un réglage qu'il a posé lui-même. La décision de
        consentement est ce qui permet d'honorer son refus&nbsp;; ne pas la conserver
        reviendrait à reposer la question indéfiniment.
      </div>
@unless (config('charte.export'))
{{-- Le site exporté, statique, ne dépose rien de plus que la charte : ce
     bloc ne vaut que pour l'application, qui sert cette page. --}}

      <div class="doc-bloc">
        <h3>Ce que ce site de documentation dépose en plus</h3>
        <p class="doc-bloc-note">
          Le tableau ci-dessus vaut pour la charte, dans n'importe quel service. Le
          site que vous lisez ajoute ce qui suit, pour se protéger des robots et
          recevoir vos messages. La même règle s'applique à lui&nbsp;: il le déclare.
        </p>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr>
                <th>Ce qui est écrit</th><th>Où</th><th>Combien de temps</th>
                <th>Consentement</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><code>charte_humain</code><br><span class="fs-faible">l'heure de la dernière page vue après la case anti-robot, chiffrée</span></td>
                <td>Cookie, <code>HttpOnly</code>, <code>SameSite=Lax</code>, réécrit à chaque page</td>
                <td>{!! \App\Support\Duree::lisible(config('charte.verification.duree')) !!} sans page vue, puis la case est redemandée</td>
                <td><span class="fs-badge fs-badge--succes">Non requis</span></td>
              </tr>
              <tr>
                <td><code>charte_session</code> et <code>XSRF-TOKEN</code><br><span class="fs-faible">session et jeton anti-falsification</span></td>
                <td>Cookies, pages Vérification et Nous écrire seulement</td>
                <td>{{ config('session.lifetime') }}&nbsp;minutes d'inactivité</td>
                <td><span class="fs-badge fs-badge--succes">Non requis</span></td>
              </tr>
              <tr>
                <td>Google reCAPTCHA<br><span class="fs-faible">la case « Je ne suis pas un robot »</span></td>
                <td>Script et cadre servis par Google, pages Vérification et Nous écrire</td>
                <td>Selon les règles de Google</td>
                <td><span class="fs-badge fs-badge--alerte">Tiers</span></td>
              </tr>
            </tbody>
          </table>
        </div>
        <p class="doc-bloc-note fs-mt-4">
          Deux écarts aux règles de cette page en découlent, et il vaut mieux les
          écrire&nbsp;: la case est un appel vers un tiers, Google, et elle demande
          JavaScript, si bien que le site ne se lit plus sans lui. Les fichiers de la
          charte, eux, restent servis sans case ni appel extérieur, et le site
          exporté en fichiers statiques n'a ni l'un ni l'autre.
        </p>
      </div>
@endunless

      <div class="doc-bloc">
        <h3>Ce que la charte ne fait jamais</h3>
        <ul class="fs-liste-puces fs-mesure">
          <li>Aucun identifiant d'appareil ni de session n'est créé par la charte.</li>
          <li>Aucune mesure d'audience n'est incluse. Celle du service lui appartient,
              et n'est déposée qu'après une réponse.</li>
          <li>Aucun appel réseau vers un tiers&nbsp;: les polices, les icônes et
              l'emblème sont servis par le domaine du service.</li>
          <li>Le stockage est toujours protégé&nbsp;: en navigation privée ou sur un site
              bloqué, la lecture échoue sans que la page en souffre.</li>
        </ul>
      </div>

      <div class="doc-bloc">
        <h3>Ce qui revient au service</h3>
        <p class="doc-bloc-note">
          Un téléservice dépose d'autres choses que la charte ne connaît pas&nbsp;:
          cookie de session, jeton anti-falsification de formulaire, mesure
          d'audience, lecteur vidéo. Les trois premiers lui sont nécessaires et ne se
          demandent pas&nbsp;; ils doivent néanmoins figurer dans sa politique de
          confidentialité. Le reste passe par le
          <a href="{{ page('composants-metier') }}#confiance">bandeau de consentement</a>.
        </p>
        <p class="doc-bloc-note">
          La protection des données à caractère personnel relève au Burkina Faso d'une
          autorité de contrôle dédiée. Les obligations déclaratives d'un téléservice,
          la durée de conservation et les mentions de sa politique de confidentialité
          s'apprécient au regard du cadre national&nbsp;: la charte fournit le
          composant et le comportement, pas la qualification juridique.
        </p>
      </div>
    </section>

    <section class="doc-section" id="conformite">
      <h2>Déclaration de conformité</h2>
      <p class="doc-section-intro">
        Avant toute mise en production, le référent de projet atteste la conformité de
        son service. La déclaration est publiée au même titre que la déclaration
        d'accessibilité.
      </p>

      <div class="doc-bloc">
        <h3>Points attestés</h3>
        <ul class="fs-liste-controle fs-mesure">
          <li>Le bandeau d'identification officielle, le filet national et le bloc-marque sont repris sans adaptation.</li>
          <li>Les trois feuilles de style du système sont intégrées sans modification locale.</li>
          <li>Aucune couleur, taille ou forme n'est écrite en dur. Toute adaptation passe par une surcharge de jetons.</li>
          <li>Les cinq vérifications d'accessibilité de la recette ont été passées.</li>
          <li>Les dérogations en vigueur sont listées, avec leur date d'échéance.</li>
          <li>La version de la charte utilisée est indiquée, ainsi que la date de l'audit.</li>
        </ul>
      </div>

      <div class="doc-bloc">
        <h3>Niveaux de conformité</h3>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Niveau</th><th>Condition</th><th>Conséquence</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="fs-badge fs-badge--succes">Conforme</span></td>
                <td>Tous les points attestés, aucune dérogation ou dérogations en cours de validité.</td>
                <td>Mise en production autorisée.</td>
              </tr>
              <tr>
                <td><span class="fs-badge fs-badge--alerte">Conforme sous réserve</span></td>
                <td>Écarts identifiés, sans incidence sur l'accessibilité ni sur l'identification officielle.</td>
                <td>Mise en production autorisée, plan de correction daté exigé.</td>
              </tr>
              <tr>
                <td><span class="fs-badge fs-badge--danger">Non conforme</span></td>
                <td>Atteinte à l'identification officielle, ou défaut d'accessibilité bloquant.</td>
                <td>Mise en production ajournée.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="journal">
      <h2>Journal des versions</h2>
      <p class="doc-section-intro">
        Chaque version publiée est consignée ici. Les ruptures sont listées en premier,
        parce que ce sont les seules qui demandent du travail aux projets.
      </p>

      <div class="doc-bloc">
        <h3>2.0&nbsp;— septembre 2026</h3>
        <p class="doc-bloc-note">
          Refonte des fondations et première bibliothèque de composants réelle.
          Version majeure. La reprise d'un projet issu de la 1.0 est nécessaire.
        </p>

        <h4 class="fs-mt-6">Ruptures</h4>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <thead>
              <tr><th>Objet</th><th>Avant</th><th>Après</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Police de titre</td>
                <td>Poppins</td>
                <td>Archivo</td>
              </tr>
              <tr>
                <td>Hébergement des polices</td>
                <td>Google Fonts</td>
                <td>Servies depuis le domaine du service, en polices variables, sous-ensemble latin&nbsp;: 81&nbsp;Ko au lieu de 198</td>
              </tr>
              <tr>
                <td>Police à chasse fixe</td>
                <td>JetBrains Mono, téléchargée</td>
                <td>Supprimée. La chasse fixe du système suffit. Elle ne porte aucune identité</td>
              </tr>
              <tr>
                <td>Neutres</td>
                <td>Gris bleutés</td>
                <td>Base chaude dérivée de la latérite</td>
              </tr>
              <tr>
                <td><code>--bleu-gouvernement</code></td>
                <td>Couleur de structure&nbsp;: en-têtes, navigation, pieds de page</td>
                <td>Supprimé. Le bleu devient couleur de support, cantonnée au registre informatif</td>
              </tr>
              <tr>
                <td><code>.fs-filet</code></td>
                <td>Trois bandes égales, 4&nbsp;px</td>
                <td>Composition du drapeau&nbsp;: moitié rouge, moitié verte, étoile d'or. 12&nbsp;px</td>
              </tr>
              <tr>
                <td>Feuilles de style</td>
                <td><code>tokens.css</code> et <code>faso.css</code></td>
                <td><code>icones.css</code> devient obligatoire&nbsp;: plusieurs composants y puisent leurs repères</td>
              </tr>
              <tr>
                <td>Thème sombre</td>
                <td>Suivait la préférence du système d'exploitation</td>
                <td>Le thème clair est le défaut&nbsp;; le sombre s'active sur choix explicite</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h4 class="fs-mt-8">Ajouts</h4>
        <ul class="fs-liste-puces fs-mesure">
          <li><strong>65 composants</strong> documentés en sept familles, là où la 1.0 ne décrivait que des jetons.</li>
          <li><strong>Iconographie</strong>&nbsp;: 35 icônes en cinq familles, grille de 24&nbsp;px, trait de 1,5, appliquées en masque CSS.</li>
          <li><strong>Emblème vectoriel</strong>&nbsp;: 45&nbsp;Ko compressé au lieu de 916&nbsp;Ko, net à toute taille.</li>
          <li><strong>Échelles tonales</strong> de dix paliers sur les trois couleurs nationales, la teinte officielle restant verrouillée à son palier.</li>
          <li><strong><code>.fs-repere</code></strong> et <strong><code>.fs-tissage</code></strong>, ce dernier inspiré des rayures du Faso Dan Fani.</li>
          <li><strong>Gabarits institutionnels</strong>&nbsp;: portail, fiche de démarche, formulaire long, tableau de bord, authentification, pages d'erreur.</li>
          <li><strong>Composants métier</strong> de confiance numérique&nbsp;: bandeau officiel, signalement de fraude, canaux de paiement autorisés.</li>
          <li><strong>Contrôle de contraste</strong> calculé dans la page, selon la formule WCAG&nbsp;2.1.</li>
          <li><strong>Panneau de code</strong> sous chaque exemple, restituant le HTML, le CSS et le JavaScript réellement en vigueur.</li>
          <li>Feuille d'impression, thème sombre, contraste renforcé et mouvement réduit traités dans le socle.</li>
        </ul>

        <h4 class="fs-mt-8">Corrections</h4>
        <ul class="fs-liste-puces fs-mesure">
          <li>
            <strong>Résolution des cibles sans ambiguïté.</strong> Six comportements
            résolvaient leur cible par identifiant global. Deux instances d'un même
            composant se disputaient alors le même <code>id</code>, et le déclencheur
            de la seconde agissait sur la première. Quand l'identifiant ne désigne
            qu'un élément, le résultat reste celui de <code>getElementById</code>&nbsp;;
            ce n'est qu'en cas de doublon que la recherche remonte depuis le
            déclencheur. La frontière d'une racine fantôme est franchie.
          </li>
          <li>
            <strong>Écritures de valeur signalées.</strong> Cinq endroits où le script
            écrit dans un champ (code collé, date collée, suggestion retenue, filtre
            vidé), émettent <code>input</code> et <code>change</code>. Sans eux, une
            liaison bidirectionnelle conservait l'ancienne valeur et le formulaire
            partait incomplet.
          </li>
          <li>
            <strong>Valeurs invisibles pour React.</strong> React n'interroge pas le
            champ. Il compare à ce que son propre accesseur a mémorisé. Une valeur
            écrite par cet accesseur (date collée, code collé, suggestion retenue,
            filtre vidé) ne déclenchait donc jamais <code>onChange</code>&nbsp;: l'usager
            lisait la nouvelle valeur à l'écran et le formulaire partait avec
            l'ancienne. La charte écrit désormais par l'accesseur du prototype, ce qui
            est sans effet pour les cadriciels qui n'ont rien installé.
          </li>
          <li>
            <strong>Consentement réellement géré.</strong> Le bandeau existait mais
            n'était qu'une image&nbsp;: trois boutons sans comportement, aucune décision
            conservée, et un texte qui promettait &laquo;&nbsp;votre choix est conservé
            six mois&nbsp;&raquo; alors que rien ne conservait. Un service qui recopiait
            ce balisage obtenait un bandeau qui reparaissait à chaque page. La
            décision est désormais recueillie finalité par finalité, conservée six
            mois dans un cookie <code>SameSite=Lax</code>, restituée par
            <code>Faso.consentement.lire()</code> et annoncée par
            <code>fs:consentement</code>. Le dépôt des traceurs reste au service.
          </li>
          <li>
            <strong>Ce que la charte dépose est déclaré.</strong> Elle écrivait le
            thème dans le stockage local depuis le début sans qu'aucune page ne le
            dise. Une section le déclare, avec la durée et le motif pour lequel ces
            deux écritures ne demandent pas de consentement.
          </li>
          <li>
            <strong>Une seule composition pour toutes les marques.</strong> Le filet
            pleine largeur et le bandeau d'une carte mise en avant étaient divisés
            gauche-droite, avec l'étoile à la jonction&nbsp;; seul le repère court portait
            la composition du drapeau. Les cinq marques du système la portent
            désormais&nbsp;: <strong>rouge au-dessus, vert au-dessous, étoile centrée à
            cheval sur les deux</strong>. Une marque de l'État ne se réarrange pas
            selon le format où elle se pose. La justification contraire, qui figurait
            dans la page Fondations et dans les commentaires du code, a été retirée.
            Le contrôle d'identité vérifie maintenant la composition marque par marque,
            et non plus sa seule présence quelque part dans la feuille.
          </li>
          <li>
            <strong>Recette exécutable et chaîne de publication.</strong> La
            vérification n'était affirmée nulle part qu'en prose.
            <code>npm test</code> exécute désormais vingt contrôles&nbsp;: syntaxe,
            balisage des neuf pages, classes orphelines, bonne formation des SVG et
            des 43 ressources Android, correspondance des projections, cohérence du
            paquet, contraste, et six suites de comportements. Un second mode,
            <code>--publication</code>, refuse de laisser partir un paquet dont les
            champs restent à renseigner ou dont les conditions de réutilisation ne
            sont pas arrêtées. Deux chaînes d'intégration, une page d'erreur 404 et
            trois configurations de serveur complètent l'ensemble.
          </li>
          <li>
            <strong>Chiffre de contrôle corrigé.</strong> La vérification de
            contraste annonçait 150 couples&nbsp;; elle comptait les couples tentés, y
            compris ceux qui ne se résolvaient pas. Le nombre de couples réellement
            éprouvés est de 118, et il est désormais produit par le contrôle
            lui-même.
          </li>
          <li>
            <strong>Intégration d'Android, en Kotlin et en Java.</strong> Le CSS et
            le JavaScript ne s'exécutent pas dans une application native&nbsp;: ce sont
            les décisions qui se transportent. <code>outils/android.js</code> produit
            depuis la même feuille de jetons les couleurs des deux thèmes, les
            dimensions du téléphone et de la tablette, un thème Material&nbsp;3, un
            thème Compose, les 35 icônes, l'emblème, le filet et le repère nationaux.
            Deux traductions y sont assumées&nbsp;: <code>clamp()</code> devient deux
            jeux de dimensions, et la cible tactile passe de 44 à 48&nbsp;dp, plancher
            d'Android. En WebView, la charte s'exécute telle quelle&nbsp;; la section
            correspondante insiste sur le refus de l'assombrissement automatique, qui
            déplacerait les couleurs du drapeau.
          </li>
          <li>
            <strong>Intégration de React et de Laravel.</strong> Les deux ont
            désormais leur section, au même niveau de détail qu'Angular&nbsp;: champs
            contrôlés et écoute des annonces pour le premier, formulaire Blade,
            paginateur d'Eloquent, Livewire, Alpine, Inertia et politique de sécurité
            pour le second. Les autres cadriciels sont renvoyés à celui des trois dont
            ils se rapprochent.
          </li>
          <li>
            <strong>Contraste des contours et des mentions.</strong> Le contour d'un
            champ atteignait 1,66:1 sur blanc, là où le référentiel en exige 3 pour
            un élément d'interface&nbsp;; le texte faible atteignait 4,16:1 pour un
            seuil de 4,5. Deux demi-paliers, <code>neutre-450</code> et
            <code>neutre-550</code>, ont été ajoutés à l'échelle et les rôles
            réaffectés. En thème sombre, le contour ne donnait que 1,96:1 et le
            texte du bouton de suppression 3,13:1&nbsp;: un jeton
            <code>accent-texte</code> résout le second. Les 118 couples de couleurs
            réellement employés par la feuille passent désormais leur seuil, dans
            les deux thèmes.
          </li>
          <li>
            <strong>Injection de formule dans les extractions.</strong> Une cellule
            valant <code>=1+1</code> ou <code>=HYPERLINK(...)</code> était livrée
            telle quelle et s'exécutait à l'ouverture du fichier sur le poste de
            l'agent. Les cellules de texte commençant par un caractère de formule
            sont désarmées par une apostrophe&nbsp;; les nombres, reconnus, restent
            calculables. L'échappement n'aplatit plus les espaces ni les retours à
            la ligne, qui appartiennent à la donnée.
          </li>
          <li>
            <strong>Surface publique utilisable au rendu serveur.</strong>
            <code>Faso.appliquerTheme()</code> et les vingt et un comportements
            exposés levaient <code>ReferenceError: document is not defined</code>.
            Seul l'import était protégé. Les vingt-huit points d'entrée sont
            désormais sans effet plutôt que fatals.
          </li>
          <li>
            <strong>Éléments ajoutés après l'initialisation.</strong> Un onglet
            ajouté à une liste déjà initialisée n'obtenait ni clic ni clavier, et
            rejouer l'initialisation n'y changeait rien&nbsp;: le marquage portait sur
            le conteneur. Il porte maintenant sur chaque enfant, et une veille
            équipe les arrivants. Même traitement pour les cases d'un code à usage
            unique et les options d'une autocomplétion.
          </li>
          <li>
            <strong>Fuite d'écouteur de l'autocomplétion.</strong> Chaque instance
            posait un écouteur sur <code>document</code> qui retenait son bloc bien
            après la destruction de celui-ci. Un écouteur unique le remplace, qui
            relit le document à chaque clic.
          </li>
          <li>
            <strong>Cible tactile tenue ou dite.</strong> La charte annonçait
            44&nbsp;×&nbsp;44&nbsp;px pour des boutons à icône de 36 et des jours de
            calendrier de 40. La case du calendrier passe à 44&nbsp;px, le bouton à
            icône reçoit une zone active débordante, et les deux exceptions qui
            subsistent sont écrites dans la page Accessibilité.
          </li>
          <li>
            <strong>Conformité annoncée ramenée au mesuré.</strong> La page
            Accessibilité affirmait que les composants satisfaisaient déjà les
            critères. Elle dit maintenant ce qui a été mesuré, et ce qui ne l'a pas
            été&nbsp;: ni lecteur d'écran, ni audit tiers, ni test avec des personnes
            en situation de handicap.
          </li>
          <li>
            <strong>Jetons produits, non recopiés.</strong> <code>jetons.json</code>
            était entretenu à la main et avait divergé du CSS sur
            <code>police-mono</code>, tandis que <code>etoile-or</code> y manquait.
            <code>outils/jetons.js</code> le produit désormais depuis
            <code>tokens.css</code>, et sa vérification entre dans la recette.
          </li>
          <li>
            <strong>Libellés du script rassemblés.</strong> Une vingtaine de textes
            étaient écrits en dur en français. Ils forment un dictionnaire unique,
            <code>Faso.textes</code>, que le projet complète. Le pays compte une
            soixantaine de langues&nbsp;: la charte ne pouvait pas en imposer une.
          </li>
          <li>
            <strong>Exemple Angular qui compile.</strong> Le gestionnaire d'annonce
            était écrit avec une signature en <code>CustomEvent</code>, refusée par
            <code>strictTemplates</code> puisque Angular type <code>$event</code> en
            <code>Event</code>, et le gabarit ne portait pas le <code>data-cle</code>
            que le gestionnaire relisait.
          </li>
          <li>
            <strong>Outillage de la documentation retiré du paquet.</strong>
            <code>docs.css</code> et <code>docs.js</code> ne servent qu'au site de la
            charte et pesaient sur chaque projet consommateur.
          </li>
          <li>
            <strong>Extraction incomplète rendue impossible.</strong>
            <code>data-export-total</code> fait refuser l'export dès que le compte des
            lignes affichées ne correspond pas au total déclaré.
          </li>
          <li>
            <strong>Pagination et filtres résistent au re-rendu.</strong> Le
            remplacement des lignes est détecté et les deux se rejouent. La page
            courante est conservée lorsque le nombre de lignes n'a pas changé&nbsp;:
            un re-rendu sans rapport ne fait plus perdre sa place à l'usager.
          </li>
          <li>
            <strong>Tout état écrit est annulable.</strong> Onglets, navigation
            repliable, jauges, barres, notifications lues et fermeture d'encart
            annoncent leur changement avant de l'écrire. Un projet qui lie l'attribut
            lui-même annule l'annonce&nbsp;: la charte continue d'assurer le clavier, le
            focus et l'ordre de tabulation, sans toucher à la liaison. Chaque annonce
            porte deux noms, <code>fs:onglet</code> et <code>fs-onglet</code>, la
            seconde forme étant la seule qu'un gabarit d'Angular puisse lier.
          </li>
          <li>
            <strong>Verrou de défilement compté.</strong> Un panneau latéral en
            remplaçait la valeur d'origine par une chaîne vide à la fermeture, et deux
            panneaux superposés libéraient le défilement trop tôt. Un panneau détruit
            sans avoir été fermé laissait la page bloquée.
          </li>
          <li>
            <strong>Valeurs suivies.</strong> Jauges et barres se mettaient à jour à
            l'initialisation seulement. Une valeur changée ensuite par le cadriciel
            restait sans effet sur la largeur affichée.
          </li>
          <li>
            <strong>Amorce du thème.</strong> Une page réglée en sombre s'affichait
            brièvement en clair. <code>faso-amorce.js</code> pose l'attribut avant le
            premier rendu, sans script en ligne.
          </li>
          <li>
            <strong>Notification au rendu côté serveur.</strong> <code>Faso.notifier()</code>
            échouait hors navigateur&nbsp;; l'appel ne fait plus rien et rend une fermeture
            inerte.
          </li>
          <li>Navigation principale inatteignable sous 900&nbsp;px, faute de bouton de repli.</li>
          <li>Zone de dépôt de fichier&nbsp;: le script doublait le clic natif du libellé et ouvrait deux fois le sélecteur.</li>
          <li>Glyphes Unicode d'avertissement rendus en emoji couleur sous Windows, remplacés par des icônes du système.</li>
          <li>Emblème de 916&nbsp;Ko chargé sur chaque page, en contradiction avec le principe de contrainte réseau.</li>
        </ul>
      </div>

      <div class="doc-bloc">
        <h3>1.0&nbsp;— avril 2026 <span class="fs-badge fs-badge--neutre">Archivée</span></h3>
        <p class="doc-bloc-note">
          Version initiale. Palette issue du drapeau, couleurs institutionnelles,
          typographie, espacement, rayons et ombres, accompagnés d'une description des
          usages institutionnels et des modèles de pages. Le système ne comportait pas
          encore de bibliothèque de composants intégrable.
        </p>

        <div class="fs-encart fs-encart--info">
          <span class="fs-encart-marque fs-icone fs-icone--dossier" aria-hidden="true"></span>
          <div class="fs-encart-corps">
            <p class="fs-encart-titre">Emplacement de l'archive</p>
            <p>
              Les fichiers de la 1.0 ont été déplacés dans
              <code>archives/v1-avril-2026/</code>, accompagnés d'une note rappelant
              qu'ils ne font plus foi. Ils cohabitaient jusque-là avec la 2.0 à la
              racine du projet, sans rien indiquer de leur obsolescence. Deux jeux
              de jetons contradictoires s'y présentaient comme également valides.
            </p>
            <p>
              Le raster d'origine de l'emblème y est conservé. C'est la source dont
              la version vectorielle a été dérivée, et elle est nécessaire pour
              refaire ou contrôler ce tracé.
            </p>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Sort des versions retirées</h3>
        <p class="doc-bloc-note">
          Une version qui cesse de faire foi n'est pas effacée. Elle est archivée.
          On peut ainsi vérifier après coup ce qu'un service conforme à une
          version antérieure était censé appliquer.
        </p>
        <ul class="fs-liste-puces fs-mesure">
          <li>Les fichiers sont déplacés dans <code>archives/&lt;version&gt;-&lt;mois&gt;/</code>, hors du chemin de production.</li>
          <li>Une note placée dans le dossier énonce, dès sa première ligne, que ces fichiers ne font plus foi et où se trouve la version en vigueur.</li>
          <li>Les sources nécessaires à la reconstitution d'un livrable (fichier d'origine d'un emblème, par exemple), sont conservées avec elle.</li>
          <li>Aucun fichier obsolète ne demeure à la racine. Deux sources contradictoires au même endroit valent absence de source.</li>
        </ul>
      </div>
    </section>

@endsection
