@extends('layouts.documentation')

@section('contenu')

    <div class="doc-entete">
      <nav class="doc-fil" aria-label="Fil d'Ariane">
        <a href="{{ page('index') }}">Charte</a> <span aria-hidden="true">/</span> <span>Composants de base</span>
      </nav>
      <h1>Composants de base</h1>
      <p class="fs-chapeau">
        Les briques communes à tous les services de l'État, réparties en sept
        familles fonctionnelles. Chaque composant est livré en HTML et CSS natifs&nbsp;:
        aucune dépendance, aucune compilation. Le code affiché sous chaque exemple
        est extrait du rendu lui-même et ne peut donc pas diverger de ce qui
        s'affiche.
      </p>
    </div>

    <!-- ===================================================== -->
    <section class="doc-section" id="actions">
      <h2>Actions</h2>
      <p class="doc-section-intro">
        Une page ne comporte qu'une seule action principale. Si deux actions se
        disputent ce rang, c'est que la page porte deux intentions et doit être
        scindée.
      </p>

      <div class="doc-bloc">
        <h3>Boutons <code>.fs-btn</code></h3>
        <p class="doc-bloc-note">
          Six variantes hiérarchisées. L'or est réservé aux mises en avant
          exceptionnelles, le rouge aux seules actions destructrices ou graves.
        </p>

        <div class="doc-demo" data-libelle="Variantes">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--principal">Déposer la demande</button>
            <button type="button" class="fs-btn fs-btn--secondaire">Enregistrer le brouillon</button>
            <button type="button" class="fs-btn fs-btn--tertiaire">Revenir à l'étape précédente</button>
            <button type="button" class="fs-btn fs-btn--relief">Accéder au service</button>
            <button type="button" class="fs-btn fs-btn--danger">Supprimer le dossier</button>
            <button type="button" class="fs-btn fs-btn--fantome">Annuler</button>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Tailles et états</h3>
        <div class="doc-demo" data-libelle="Tailles">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--principal fs-btn--sm">Petit</button>
            <button type="button" class="fs-btn fs-btn--principal">Standard</button>
            <button type="button" class="fs-btn fs-btn--principal fs-btn--lg">Grand</button>
            <button type="button" class="fs-btn fs-btn--principal" disabled>Indisponible</button>
            <button type="button" class="fs-btn fs-btn--principal" data-chargement="true">Envoi en cours</button>
            <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--icone" aria-label="Imprimer la fiche"><span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span></button>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Un bouton réduit à une icône porte obligatoirement un <code>aria-label</code>.
          Pendant un chargement, le libellé reste en place dans le DOM&nbsp;: il est masqué
          visuellement mais toujours annoncé.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Boutons sur fond coloré</h3>
        <p class="doc-bloc-note">
          Sur un aplat sombre ou coloré, les variantes courantes perdent leur
          contraste. Deux variantes dédiées prennent le relais.
        </p>
        <div class="doc-demo" data-libelle="Sur fond sombre">
          <div class="doc-demo-scene doc-demo-scene--sombre">
            <button type="button" class="fs-btn fs-btn--relief">Accéder au service</button>
            <button type="button" class="fs-btn fs-btn--inverse">Consulter la notice</button>
            <button type="button" class="fs-btn fs-btn--contour-clair">En savoir plus</button>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Groupe et contrôle segmenté</h3>
        <div class="doc-demo" data-libelle="Groupes d'actions">
          <div class="doc-demo-scene">
            <div class="fs-btn-groupe" role="group" aria-label="Format d'export">
              <button type="button" class="fs-btn fs-btn--tertiaire">PDF</button>
              <button type="button" class="fs-btn fs-btn--tertiaire">CSV</button>
              <button type="button" class="fs-btn fs-btn--tertiaire">Impression</button>
            </div>

            <div class="fs-segment" role="group" aria-label="Période affichée">
              <button type="button" aria-pressed="true">Mois</button>
              <button type="button" aria-pressed="false">Trimestre</button>
              <button type="button" aria-pressed="false">Année</button>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Barre d'actions <code>.fs-actions</code></h3>
        <p class="doc-bloc-note">
          Sur écran large, l'action principale est à droite, après les actions
          secondaires. Sur écran étroit, l'ordre s'inverse et les boutons passent en
          pleine largeur&nbsp;: le pouce atteint d'abord ce qui compte.
        </p>
        <div class="doc-demo" data-libelle="Barre d'actions">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-actions fs-actions--fin">
              <button type="button" class="fs-btn fs-btn--fantome">Annuler</button>
              <button type="button" class="fs-btn fs-btn--tertiaire">Enregistrer</button>
              <button type="button" class="fs-btn fs-btn--principal">Valider et transmettre</button>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Liens <code>a</code></h3>
        <div class="doc-demo" data-libelle="Liens">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <p>
              Consultez la <a href="#">notice explicative</a> avant de commencer.
              Le texte intégral est publié sur le
              <a class="fs-lien-externe" href="#">Journal officiel</a>.
            </p>
            <p>
              <a class="fs-lien-fichier" href="#">
                Formulaire de demande <span class="fs-poids">PDF&nbsp;— 240&nbsp;Ko</span>
              </a>
            </p>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Un lien vers un fichier annonce toujours son format et son poids&nbsp;: sur une
          connexion facturée au volume, cette information conditionne la décision de
          l'usager.
        </p>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="formulaires">
      <h2>Formulaires</h2>
      <p class="doc-section-intro">
        Le formulaire est le point de contact le plus exigeant entre l'usager et
        l'administration. Chaque champ porte une étiquette persistante, une aide
        placée avant la saisie, et un message d'erreur qui indique la correction
        attendue plutôt que de constater la faute.
      </p>

      <div class="doc-bloc">
        <h3>Champ de saisie <code>.fs-champ</code></h3>
        <div class="doc-demo" data-libelle="Champ de texte">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div class="fs-champ">
              <label class="fs-label" for="cnib">
                Numéro CNIB <span class="fs-requis" aria-hidden="true">*</span>
              </label>
              <p class="fs-aide" id="cnib-aide">
                Le numéro figure au recto de votre carte, sous la photographie.
                Format. Une lettre suivie de sept chiffres.
              </p>
              <input class="fs-saisie" type="text" id="cnib" name="cnib"
                     placeholder="B1234567" aria-describedby="cnib-aide" required>
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Signaler un champ obligatoire par un astérisque rouge placé après le libellé, et ne rien ajouter devant les champs facultatifs (guide, § 6.1.1). L'astérisque est masqué au lecteur d'écran&nbsp;: c'est l'attribut <code>required</code> qui l'annonce.</li>
              <li>Cadrer la saisie à gauche, chiffres compris (guide, § 6.1.1).</li>
              <li>Donner au champ une largeur proportionnelle au nombre de caractères attendus&nbsp;: les trois champs de la saisie de date en sont l'exemple.</li>
              <li>Préremplir le champ chaque fois que c'est possible, avec la valeur la plus probable ou celle que l'usager a déjà donnée dans le même contexte (guide, § 6.1.1). Un texte indicatif (<code>placeholder</code>) n'est pas une valeur par défaut&nbsp;: il disparaît à la frappe et n'est pas transmis.</li>
              <li>Faire figurer l'unité dans le libellé («&nbsp;Montant à régler (F&nbsp;CFA)&nbsp;»)&nbsp;: l'usager ne la saisit jamais (guide, § 6.1.1 et § 4.4.4).</li>
              <li>Montrer le format attendu des dates, montants et numéros de téléphone par un exemple réel, puis accepter la saisie avec ou sans séparateurs et la remettre en forme après coup (guide, § 2.4.1.4 et § 6.1.1).</li>
              <li>Masquer la saisie d'un champ confidentiel (<code>type="password"</code>) lorsque sa nature l'exige (guide, § 7.3).</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Forcer la casse&nbsp;: ni conversion en majuscules à la frappe, ni refus des minuscules (guide, § 6.1.1). Le service normalise la valeur à l'enregistrement s'il en a besoin.</li>
              <li>Écrire un libellé en capitales. Seule l'initiale du premier mot prend la majuscule (guide, § 2.4).</li>
              <li>Faire saisir l'unité, ou la placer seulement dans l'aide.</li>
              <li>Un champ de 600&nbsp;px de large pour un code de six caractères.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>États de validation</h3>
        <div class="doc-demo" data-libelle="Validation">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div class="fs-champ" data-etat="erreur">
              <label class="fs-label" for="courriel-ko">Adresse électronique</label>
              <input class="fs-saisie" type="email" id="courriel-ko" value="usager.exemple"
                     aria-invalid="true" aria-describedby="courriel-ko-erreur">
              <p class="fs-message fs-message--erreur" id="courriel-ko-erreur">
                Ajoutez le symbole arobase et le nom de domaine, par exemple
                nom@exemple.bf
              </p>
            </div>

            <div class="fs-champ" data-etat="succes">
              <label class="fs-label" for="courriel-ok">Adresse électronique</label>
              <input class="fs-saisie" type="email" id="courriel-ok" value="a.ouedraogo@exemple.bf"
                     aria-describedby="courriel-ok-message">
              <p class="fs-message fs-message--succes" id="courriel-ok-message">
                Adresse vérifiée
              </p>
            </div>

            <div class="fs-champ">
              <label class="fs-label" for="inactif">Numéro de dossier <span class="fs-optionnel">facultatif</span></label>
              <input class="fs-saisie" type="text" id="inactif" value="Attribué après dépôt" disabled>
            </div>

            <div class="fs-champ">
              <label class="fs-label" for="lecture-seule">Commune d'enrôlement</label>
              <input class="fs-saisie" type="text" id="lecture-seule" value="Ouagadougou" readonly>
            </div>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Un champ en lecture seule (<code>readonly</code>) prend un fond gris&nbsp;: la
          zone d'affichage ne doit pas ressembler à une zone de saisie (guide, § 2.2
          et § 6.1.1). Il reste atteignable à la tabulation, ce qui laisse le lecteur
          d'écran en lire la valeur, et celle-ci est transmise avec le formulaire. Une
          donnée qui n'est que consultée, hors formulaire, s'affiche en texte (liste de
          définitions), pas dans un champ&nbsp;: le focus ne doit pas s'arrêter sur un
          champ d'affichage (guide, § 5.1.1).
        </p>

        <h4 class="fs-mt-8">Quand et comment signaler une erreur</h4>
        <p class="doc-bloc-note">
          Le script de la charte ne valide aucun champ&nbsp;: les contrôles sont écrits par
          le service, et vérifiés de nouveau par le serveur. Ils suivent les règles du
          guide (§ 6.1.1).
        </p>
        <div class="doc-usages">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Détecter une erreur de format pendant la saisie du champ ou au moment de le quitter, pas au dernier écran.</li>
              <li>Signaler les erreurs de fond (dossier introuvable, pièce non conforme) après la validation, une fois les données contrôlées par l'application.</li>
              <li>Placer le focus sur le champ en erreur, ou sur le récapitulatif d'erreurs lorsqu'il y en a plusieurs.</li>
              <li>Laisser la saisie en place pour que l'usager corrige seulement ce qui est faux.</li>
              <li>Fermer sans aucun message d'erreur quand l'usager choisit «&nbsp;Annuler&nbsp;».</li>
              <li>Ne créer, modifier ni supprimer aucune donnée sans une validation explicite de l'usager&nbsp;: bouton d'envoi, ou boîte de confirmation pour une opération irréversible.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Vider un champ en erreur, ou tout le formulaire.</li>
              <li>Un message qui juge l'usager&nbsp;: «&nbsp;faute&nbsp;», «&nbsp;mauvaise manipulation&nbsp;», «&nbsp;illégal&nbsp;» (guide, § 7.1.1). Le message dit ce qu'il faut corriger.</li>
              <li>Enregistrer une donnée à la frappe ou au changement d'un choix, avant toute action de l'usager.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Zone de texte et compteur</h3>
        <div class="doc-demo" data-libelle="Zone de texte">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 560px;">
            <div class="fs-champ">
              <label class="fs-label" for="motif">Motif de la demande</label>
              <p class="fs-aide">Décrivez en quelques phrases l'objet de votre démarche.</p>
              <textarea class="fs-zone" id="motif" maxlength="400" data-compteur="motif-reste"></textarea>
              <p class="fs-message fs-legende" id="motif-reste" aria-live="polite"></p>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Liste déroulante et champ composé</h3>
        <div class="doc-demo" data-libelle="Sélection">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div class="fs-champ">
              <label class="fs-label" for="region">
                Région de résidence <span class="fs-requis" aria-hidden="true">*</span>
              </label>
              <select class="fs-select" id="region" required>
                <option value="">Sélectionnez une région</option>
                <option>Boucle du Mouhoun</option>
                <option>Centre</option>
                <option>Est</option>
                <option>Hauts-Bassins</option>
                <option>Sahel</option>
              </select>
            </div>

            <div class="fs-champ">
              <label class="fs-label" for="region-origine">Région d'origine <span class="fs-optionnel">facultatif</span></label>
              <select class="fs-select" id="region-origine">
                <option value="" selected>Aucune</option>
                <option>Boucle du Mouhoun</option>
                <option>Centre</option>
                <option>Est</option>
                <option>Hauts-Bassins</option>
                <option>Sahel</option>
              </select>
            </div>

            <div class="fs-champ">
              <label class="fs-label" for="montant">Montant à régler (F&nbsp;CFA)</label>
              <div class="fs-groupe-saisie">
                <input class="fs-saisie fs-tabulaire" type="text" id="montant" value="5 000" inputmode="numeric">
                <span class="fs-affixe">F&nbsp;CFA</span>
              </div>
            </div>

            <div class="fs-champ">
              <label class="fs-label" for="tel">Téléphone</label>
              <div class="fs-groupe-saisie">
                <span class="fs-affixe">+226</span>
                <input class="fs-saisie fs-tabulaire" type="tel" id="tel" placeholder="70 00 00 00">
              </div>
            </div>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          L'unité est dans le libellé, que lit le lecteur d'écran&nbsp;; l'affixe
          «&nbsp;F&nbsp;CFA&nbsp;» ne fait que la rappeler à côté de la valeur. L'indicatif
          «&nbsp;+226&nbsp;» est affiché pour la même raison&nbsp;: l'usager ne le tape pas.
        </p>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Pour une liste facultative, placer «&nbsp;Aucun&nbsp;» (ou «&nbsp;Aucune&nbsp;») en première ligne, sélectionné par défaut (guide, § 6.2).</li>
              <li>Pour une liste obligatoire, présélectionner la valeur la plus probable quand elle existe&nbsp;; sinon, une première ligne vide qui invite à choisir.</li>
              <li>Ranger les options dans l'ordre qui a du sens pour l'usager, à défaut dans l'ordre alphabétique (guide, § 6.2 et § 4.4.3).</li>
              <li>Permettre de défaire un choix aussi simplement qu'on l'a fait&nbsp;: dans une liste à sélection multiple, un second appui désélectionne (guide, § 6.2).</li>
              <li>Afficher le nombre d'éléments sélectionnés dans une liste à sélection multiple, par exemple «&nbsp;3 états retenus&nbsp;».</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Déclencher une action au choix d'une option&nbsp;: ni envoi, ni enregistrement, ni changement de page (guide, § 6.2). L'action reste portée par un bouton.</li>
              <li>Ramener l'usager au début de la tâche quand il désélectionne un élément.</li>
              <li>Une liste facultative sans option «&nbsp;Aucun&nbsp;»&nbsp;: une fois un choix fait, l'usager ne peut plus revenir à «&nbsp;rien&nbsp;».</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Cases à cocher et boutons radio</h3>
        <div class="doc-demo" data-libelle="Choix">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 520px;">
            <fieldset class="fs-fieldset">
              <legend>Canal de retrait</legend>
              <label class="fs-choix">
                <input type="radio" name="retrait" checked>
                <span class="fs-choix-texte">
                  <span class="fs-choix-titre">Retrait au guichet</span>
                  <span class="fs-choix-aide">Mairie de résidence, du lundi au vendredi, de 7&nbsp;h 30 à 15&nbsp;h 30</span>
                </span>
              </label>
              <label class="fs-choix">
                <input type="radio" name="retrait">
                <span class="fs-choix-texte">
                  <span class="fs-choix-titre">Envoi postal</span>
                  <span class="fs-choix-aide">Frais supplémentaires de 1&nbsp;500&nbsp;F&nbsp;CFA, délai de 5 à 10&nbsp;jours</span>
                </span>
              </label>
            </fieldset>

            <label class="fs-choix">
              <input type="checkbox">
              <span class="fs-choix-texte">
                <span class="fs-choix-titre">Je certifie l'exactitude des informations fournies</span>
                <span class="fs-choix-aide">Toute fausse déclaration expose à des poursuites.</span>
              </span>
            </label>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Boutons radio pour un choix exclusif, entre deux options au moins (guide, § 6.1.3).</li>
              <li>Toujours une option cochée par défaut. Si l'usager peut ne rien choisir, ajouter une option «&nbsp;Aucun&nbsp;» ou son équivalent, plutôt que de laisser le groupe vide.</li>
              <li>Regrouper les options d'une même question dans un <code>&lt;fieldset&gt;</code> titré par sa <code>&lt;legend&gt;</code>&nbsp;: c'est la boîte de groupe du guide.</li>
              <li>Libellé à droite de la case ou du bouton, cliquable sur toute sa longueur.</li>
              <li>N'afficher que les cases pertinentes dans le contexte&nbsp;; une case affichée mais inutilisable est grisée (<code>disabled</code>), libellé compris.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Une case à cocher pour un choix fonctionnel important, comme enregistrer ou transmettre (guide, § 6.1.3)&nbsp;: c'est le rôle d'un bouton. Une attestation sur l'honneur reste une case, puisqu'elle ne déclenche rien.</li>
              <li>Un bouton radio isolé, qu'on ne peut plus décocher une fois coché.</li>
              <li>Le libellé à gauche de la case.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Choix en carte</h3>
        <p class="doc-bloc-note">
          Lorsque le choix engage la suite du parcours, la cible cliquable est
          étendue à toute la carte&nbsp;: sous le pouce, elle devient bien plus large
          que la case elle-même.
        </p>
        <div class="doc-demo" data-libelle="Choix en carte">
          <div class="doc-demo-scene" style="align-items: stretch;">
            <label class="fs-choix fs-choix--carte" style="flex:1; min-width:220px;">
              <input type="radio" name="qualite" checked>
              <span class="fs-choix-texte">
                <span class="fs-choix-titre">Pour moi-même</span>
                <span class="fs-choix-aide">Vous êtes le titulaire de l'acte demandé.</span>
              </span>
            </label>
            <label class="fs-choix fs-choix--carte" style="flex:1; min-width:220px;">
              <input type="radio" name="qualite">
              <span class="fs-choix-texte">
                <span class="fs-choix-titre">Pour un tiers</span>
                <span class="fs-choix-aide">Une procuration vous sera demandée.</span>
              </span>
            </label>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Interrupteur et curseur</h3>
        <div class="doc-demo" data-libelle="Interrupteur">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <label class="fs-interrupteur">
              <input type="checkbox" checked>
              <span class="fs-interrupteur-piste"></span>
              <span>Recevoir les notifications par SMS</span>
            </label>

            <label class="fs-interrupteur">
              <input type="checkbox">
              <span class="fs-interrupteur-piste"></span>
              <span>Afficher l'aide sous chaque champ</span>
            </label>

            <div class="fs-champ">
              <label class="fs-label" for="distance">Rayon de recherche&nbsp;: 25&nbsp;km</label>
              <input class="fs-curseur" type="range" id="distance" min="5" max="100" step="5" value="25">
            </div>
          </div>
        </div>
        <div class="doc-note fs-mt-4">
          Un interrupteur applique son effet immédiatement&nbsp;: il est réservé aux
          préférences réversibles, sans conséquence sur un dossier. Une autorisation
          de transmission, un consentement, ou tout changement qui crée, modifie ou
          supprime une donnée d'un dossier exige une validation explicite (guide,
          § 6.1.1)&nbsp;: une case à cocher suivie d'un bouton.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Dépôt de fichier <code>.fs-depot</code></h3>
        <div class="doc-demo" data-libelle="Dépôt de fichier">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 560px;">
            <div class="fs-champ">
              <label class="fs-label" for="piece">Copie de la pièce d'identité</label>
              <label class="fs-depot" tabindex="0">
                <span class="fs-depot-titre">Déposez un fichier ou parcourez</span>
                <span class="fs-depot-contraintes">JPG, PNG ou PDF (5&nbsp;Mo maximum), document lisible et non rogné</span>
                <input type="file" id="piece" accept=".jpg,.png,.pdf">
              </label>
            </div>

            <div class="fs-fichier">
              <span class="fs-fichier-nom">cnib-recto.jpg</span>
              <span class="fs-fichier-poids">1,2&nbsp;Mo</span>
              <span class="fs-badge fs-badge--succes">Transmis</span>
              <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Retirer le fichier cnib-recto.jpg"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
            </div>

            <div class="fs-fichier">
              <span class="fs-fichier-nom">justificatif-domicile.pdf</span>
              <span class="fs-fichier-poids">8,4&nbsp;Mo</span>
              <span class="fs-badge fs-badge--danger">Trop volumineux</span>
              <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Retirer le fichier justificatif-domicile.pdf"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Aperçu de fichier <code>.fs-fichier--apercu</code></h3>
        <p class="doc-bloc-note">
          La vignette rassure sur ce qui a été déposé&nbsp;: le bon document, dans le bon
          sens, lisible. C'est ce qui évite l'aller-retour le plus fréquent d'un
          service d'enrôlement. Elle ne remplace pas le nom du fichier, qui reste
          l'information portée au lecteur d'écran&nbsp;; la vignette est donc décorative.
        </p>

        <div class="doc-demo" data-libelle="Aperçu de fichier">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 520px;">

            <div class="fs-fichier fs-fichier--apercu">
              <span class="fs-fichier-vignette" aria-hidden="true">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 35 45'%3E%3Crect width='35' height='45' fill='%23cfc8bf'/%3E%3Ccircle cx='17.5' cy='17' r='7' fill='%23a9a199'/%3E%3Cpath d='M4 45c2-9 7.5-13 13.5-13S29 36 31 45z' fill='%23a9a199'/%3E%3C/svg%3E" alt="">
              </span>
              <div class="fs-fichier-detail">
                <span class="fs-fichier-nom">cnib-recto.jpg</span>
                <span class="fs-fichier-poids">1,2&nbsp;Mo&nbsp;— 1&nbsp;240 × 780&nbsp;px</span>
                <span class="fs-statut fs-statut--succes">Lisible, prêt à être transmis</span>
                <div class="fs-fichier-actions">
                  <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm">
                    <span class="fs-icone fs-icone--oeil" aria-hidden="true"></span>
                    Agrandir
                  </button>
                  <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm">Remplacer</button>
                </div>
              </div>
            </div>

            <div class="fs-fichier fs-fichier--apercu">
              <span class="fs-fichier-vignette" aria-hidden="true">
                <span class="fs-icone fs-icone--document"></span>
              </span>
              <div class="fs-fichier-detail">
                <span class="fs-fichier-nom">quittance-timbre.pdf</span>
                <span class="fs-fichier-poids">84&nbsp;Ko&nbsp;— 1 page</span>
                <span class="fs-statut fs-statut--cours">Vérification en cours</span>
              </div>
            </div>

            <div class="fs-fichier fs-fichier--apercu">
              <span class="fs-fichier-vignette" aria-hidden="true">
                <span class="fs-icone fs-icone--alerte"></span>
              </span>
              <div class="fs-fichier-detail">
                <span class="fs-fichier-nom">justificatif.jpg</span>
                <span class="fs-fichier-poids">2,1&nbsp;Mo&nbsp;— 480 × 320&nbsp;px</span>
                <span class="fs-statut fs-statut--rejet">Résolution insuffisante, document illisible</span>
                <div class="fs-fichier-actions">
                  <button type="button" class="fs-btn fs-btn--secondaire fs-btn--sm">Déposer une autre copie</button>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="doc-note fs-mt-6">
          Un PDF n'a pas de vignette&nbsp;: le calculer coûterait bien plus qu'il ne
          rapporte. Une icône de document suffit, puisque le nom et le nombre de
          pages portent déjà l'information utile.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Recherche et code de vérification</h3>
        <div class="doc-demo" data-libelle="Recherche et code">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 520px;">
            <div class="fs-champ">
              <label class="fs-label" for="q">Rechercher une démarche</label>
              <div class="fs-recherche">
                <input class="fs-saisie" type="search" id="q" placeholder="Acte de naissance, passeport, permis…">
                <button type="submit" class="fs-btn fs-btn--principal">Rechercher</button>
              </div>
            </div>

            <div class="fs-champ">
              <label class="fs-label" for="code-1">Code reçu par SMS</label>
              <p class="fs-aide">Six chiffres envoyés au +226&nbsp;70 •• •• 42</p>
              <div class="fs-code">
                <input type="text" id="code-1" inputmode="numeric" maxlength="1" aria-label="Chiffre 1">
                <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 2">
                <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 3">
                <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 4">
                <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 5">
                <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 6">
              </div>
            </div>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Un champ de longueur fixe passe la main au suivant dès qu'il est complet
          (guide, § 5.1.3). Dans <code>.fs-code</code>, le script ne garde que les
          chiffres, avance d'une case à chaque chiffre, revient à la case précédente
          sur un retour arrière dans une case vide, et répartit sur toutes les cases
          un code collé d'un bloc.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Saisie de date <code>.fs-date</code></h3>
        <p class="doc-bloc-note">
          Deux composants pour deux usages que l'on confond souvent. Une date que
          l'usager connaît par cœur se saisit au clavier en trois champs&nbsp;: ouvrir un
          calendrier pour remonter quarante ans est une épreuve. Une date à choisir
          dans les semaines qui viennent se prend dans le sélecteur natif du système,
          déjà connu de l'usager.
        </p>
        <p class="doc-bloc-note">
          Les trois champs sont le masque de saisie que demande le guide pour les
          dates (§ 2.4.1.4 et § 6.1.1)&nbsp;: le séparateur n'est jamais tapé. Dans
          <code>.fs-date</code>, le script écarte ce qui n'est pas un chiffre, passe au
          champ suivant quand le champ atteint sa longueur (<code>maxlength</code>) en
          sélectionnant son contenu, revient au champ précédent sur un retour arrière
          dans un champ vide, et répartit une date collée sur les trois champs. Un
          jour tapé «&nbsp;7&nbsp;» ne fait donc pas avancer&nbsp;: l'usager passe au mois par
          la tabulation.
        </p>

        <div class="doc-demo" data-libelle="Date connue de l'usager">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <fieldset class="fs-fieldset" style="border:0; padding:0;">
              <legend class="fs-label">Date de naissance <span class="fs-requis" aria-hidden="true">*</span></legend>
              <p class="fs-aide" id="naissance-aide">Telle qu'elle figure sur votre CNIB. Par exemple&nbsp;: 07 11 1984</p>
              <div class="fs-date" aria-describedby="naissance-aide">
                <div class="fs-champ fs-date-jour">
                  <label class="fs-label" for="n-jour">Jour</label>
                  <input class="fs-saisie" id="n-jour" inputmode="numeric" maxlength="2" placeholder="07">
                </div>
                <div class="fs-champ fs-date-mois">
                  <label class="fs-label" for="n-mois">Mois</label>
                  <input class="fs-saisie" id="n-mois" inputmode="numeric" maxlength="2" placeholder="11">
                </div>
                <div class="fs-champ fs-date-annee">
                  <label class="fs-label" for="n-annee">Année</label>
                  <input class="fs-saisie" id="n-annee" inputmode="numeric" maxlength="4" placeholder="1984">
                </div>
              </div>
            </fieldset>
          </div>
        </div>

        <div class="doc-demo fs-mt-6" data-libelle="Date à choisir">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div class="fs-champ">
              <label class="fs-label" for="rdv">Date du rendez-vous</label>
              <p class="fs-aide">Créneaux ouverts du lundi au vendredi, dans les trente prochains jours.</p>
              <input class="fs-saisie" type="date" id="rdv" min="2026-09-21" max="2026-10-21">
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Trois champs séparés pour une date de naissance, de délivrance ou d'expiration d'un titre.</li>
              <li><code>type="date"</code> pour un rendez-vous ou une échéance proche, avec <code>min</code> et <code>max</code>.</li>
              <li>Donner un exemple concret de format dans l'aide, jamais seulement « JJ/MM/AAAA ».</li>
              <li>Accepter une date collée depuis le presse-papiers et la répartir sur les trois champs.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Un calendrier pour saisir une date de naissance.</li>
              <li>Trois listes déroulantes&nbsp;: une liste d'années de 1900 à 2026 est inutilisable au pouce.</li>
              <li>Refuser la saisie d'un chiffre au motif que la date serait invalide. On valide à la sortie du champ, pas à la frappe.</li>
              <li>Imposer les zéros de tête&nbsp;: « 7 » vaut « 07 ».</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Calendrier <code>.fs-calendrier</code></h3>
        <p class="doc-bloc-note">
          Pour choisir un créneau parmi ceux qu'une administration a ouverts&nbsp;— là où
          la saisie en trois champs ne convient pas, puisque l'usager ne connaît pas
          la date à l'avance. Un calendrier est un tableau&nbsp;: les jours d'une même
          colonne partagent un en-tête, ceux d'une même ligne une semaine. Le balisage
          suit cette structure, ce qui le rend parcourable sans traitement particulier.
        </p>

        <div class="doc-demo" data-libelle="Calendrier">
          <div class="doc-demo-scene">
            <div class="fs-calendrier">
              <div class="fs-calendrier-entete">
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Mois précédent">
                  <span class="fs-icone fs-icone--chevron-gauche" aria-hidden="true"></span>
                </button>
                <span class="fs-calendrier-mois" id="mois-courant">septembre 2026</span>
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Mois suivant">
                  <span class="fs-icone fs-icone--chevron-droite" aria-hidden="true"></span>
                </button>
              </div>

              <table>
                <caption class="fs-invisible">Créneaux disponibles en septembre 2026</caption>
                <thead>
                  <tr>
                    <th><abbr title="lundi">L</abbr></th>
                    <th><abbr title="mardi">M</abbr></th>
                    <th><abbr title="mercredi">M</abbr></th>
                    <th><abbr title="jeudi">J</abbr></th>
                    <th><abbr title="vendredi">V</abbr></th>
                    <th><abbr title="samedi">S</abbr></th>
                    <th><abbr title="dimanche">D</abbr></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="1 septembre, passé">1</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="2 septembre, passé">2</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="3 septembre, passé">3</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="4 septembre, passé">4</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="5 septembre, passé">5</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="6 septembre, passé">6</button></td>
                  </tr>
                  <tr>
                    <td><button type="button" class="fs-jour" disabled aria-label="7 septembre, passé">7</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="8 septembre, passé">8</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="9 septembre, passé">9</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="10 septembre, passé">10</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="11 septembre, passé">11</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="12 septembre, passé">12</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="13 septembre, passé">13</button></td>
                  </tr>
                  <tr>
                    <td><button type="button" class="fs-jour" disabled aria-label="14 septembre, passé">14</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="15 septembre, passé">15</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="16 septembre, passé">16</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="17 septembre, passé">17</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="18 septembre, passé">18</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="19 septembre, passé">19</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="20 septembre, passé">20</button></td>
                  </tr>
                  <tr>
                    <td>
                      <button type="button" class="fs-jour" data-aujourdhui aria-current="date"
                              aria-label="21 septembre, aujourd'hui, aucun créneau" disabled>21</button>
                    </td>
                    <td>
                      <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false"
                              data-date-libelle="mardi 22 septembre 2026"
                              aria-label="22 septembre, créneaux disponibles">22</button>
                    </td>
                    <td>
                      <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false"
                              data-date-libelle="mercredi 23 septembre 2026"
                              aria-label="23 septembre, créneaux disponibles">23</button>
                    </td>
                    <td><button type="button" class="fs-jour" disabled aria-label="24 septembre, complet">24</button></td>
                    <td>
                      <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false"
                              data-date-libelle="vendredi 25 septembre 2026"
                              aria-label="25 septembre, créneaux disponibles">25</button>
                    </td>
                    <td><button type="button" class="fs-jour" disabled aria-label="26 septembre, fermé">26</button></td>
                    <td><button type="button" class="fs-jour" disabled aria-label="27 septembre, fermé">27</button></td>
                  </tr>
                  <tr>
                    <td>
                      <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false"
                              data-date-libelle="lundi 28 septembre 2026"
                              aria-label="28 septembre, créneaux disponibles">28</button>
                    </td>
                    <td>
                      <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false"
                              data-date-libelle="mardi 29 septembre 2026"
                              aria-label="29 septembre, créneaux disponibles">29</button>
                    </td>
                    <td><button type="button" class="fs-jour" disabled aria-label="30 septembre, complet">30</button></td>
                    <td></td><td></td><td></td><td></td>
                  </tr>
                </tbody>
              </table>

              <p class="fs-calendrier-legende">
                <span>Point vert&nbsp;: créneaux disponibles</span>
                <span>Barré&nbsp;: complet ou fermé</span>
              </p>
            </div>
          </div>
        </div>

        <p class="fs-legende fs-mt-4" aria-live="polite">
          Date retenue&nbsp;: <strong data-calendrier-valeur>aucune</strong>
        </p>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Produire le mois côté serveur&nbsp;: sans JavaScript, le calendrier reste consultable et les jours ouverts restent atteignables.</li>
              <li>Donner à chaque jour un <code>aria-label</code> complet&nbsp;: le chiffre seul ne dit ni le mois ni la disponibilité.</li>
              <li>Distinguer le jour courant du jour choisi&nbsp;: ce sont deux repères différents, qui peuvent tomber sur la même case.</li>
              <li>Doubler la disponibilité d'un repère non chromatique&nbsp;: point pour ouvert, barré pour complet.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Employer un calendrier pour une date que l'usager connaît par cœur&nbsp;: la saisie en trois champs est faite pour ça.</li>
              <li>Afficher un mois sans indiquer ce qui y est disponible&nbsp;: l'usager tâtonne de case en case.</li>
              <li>Rendre un jour complet cliquable pour afficher ensuite un message d'échec.</li>
              <li>Masquer les jours indisponibles&nbsp;: la grille du mois devient illisible.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Autocomplétion <code>.fs-combo</code></h3>
        <p class="doc-bloc-note">
          Pour choisir dans une liste trop longue pour une liste déroulante&nbsp;: communes,
          administrations, démarches. <strong>En dessous de quinze entrées, une
          liste déroulante&nbsp;; au-delà, une autocomplétion</strong>.
          Sans JavaScript, le champ reste une saisie libre que le serveur valide&nbsp;— la
          liste n'est qu'une aide.
        </p>

        <div class="doc-demo" data-libelle="Autocomplétion">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div class="fs-champ fs-combo" data-combo>
              <label class="fs-label" for="commune">Commune de naissance</label>
              <p class="fs-aide">Saisissez les premières lettres, puis choisissez dans la liste.</p>
              <input class="fs-saisie" id="commune" aria-describedby="commune-nb" aria-controls="commune-liste">
              <p class="fs-invisible" id="commune-nb" role="status" aria-live="polite"></p>
              <ul class="fs-combo-liste" id="commune-liste" hidden>
                <li class="fs-combo-option">Ouagadougou</li>
                <li class="fs-combo-option">Bobo-Dioulasso</li>
                <li class="fs-combo-option">Koudougou</li>
                <li class="fs-combo-option">Banfora</li>
                <li class="fs-combo-option">Ouahigouya</li>
                <li class="fs-combo-option">Kaya</li>
                <li class="fs-combo-option">Tenkodogo</li>
                <li class="fs-combo-option">Fada N'Gourma</li>
                <li class="fs-combo-option">Dédougou</li>
                <li class="fs-combo-option">Dori</li>
                <li class="fs-combo-option">Gaoua</li>
                <li class="fs-combo-option">Manga</li>
                <li class="fs-combo-option">Ziniaré</li>
                <li class="fs-combo-vide" hidden>Aucune commune ne correspond. Vérifiez l'orthographe ou saisissez le nom complet.</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          La recherche ignore les accents&nbsp;: « dedougou » trouve « Dédougou ». Sur un
          clavier de téléphone d'entrée de gamme, exiger les accents écarte une part
          des usagers.
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Annoncer le nombre de résultats dans une région live&nbsp;: sans cela, un usager au lecteur d'écran ignore que la liste a changé.</li>
              <li>Mettre en évidence la portion saisie dans chaque suggestion.</li>
              <li>Proposer une issue explicite quand rien ne correspond.</li>
              <li>Laisser le focus dans le champ et désigner la suggestion courante par <code>aria-activedescendant</code>.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Déplacer le focus dans la liste&nbsp;: l'usager ne peut plus corriger sa saisie.</li>
              <li>Vider le champ quand la saisie ne correspond à rien.</li>
              <li>Exiger une correspondance exacte pour soumettre le formulaire.</li>
              <li>Employer l'autocomplétion pour moins de quinze entrées.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Récapitulatif d'erreurs</h3>
        <p class="doc-bloc-note">
          Placé en tête de formulaire après une soumission invalide, il reçoit le
          focus et liste chaque erreur sous forme de lien vers le champ concerné.
          Sur un long formulaire, c'est la seule façon de ne pas laisser l'usager
          chercher. Chaque lien place le curseur dans le champ en erreur, comme le
          demande le guide (§ 6.1.1), et les valeurs saisies restent en place pour
          une correction partielle.
        </p>
        <div class="doc-demo" data-libelle="Récapitulatif d'erreurs">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 560px;">
            <div class="fs-recap-erreurs" role="alert" tabindex="-1">
              <h3>Trois informations doivent être corrigées</h3>
              <ul>
                <li><a href="#cnib">Numéro CNIB&nbsp;: le format attendu est une lettre suivie de sept chiffres</a></li>
                <li><a href="#courriel-ko">Adresse électronique&nbsp;: ajoutez le symbole arobase</a></li>
                <li><a href="#piece">Pièce d'identité. Aucun fichier n'a été transmis</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="navigation">
      <h2>Navigation</h2>
      <p class="doc-section-intro">
        L'usager doit savoir en permanence où il se trouve, d'où il vient et ce
        qu'il lui reste à faire. Ces trois réponses sont portées respectivement par
        l'état actif, le fil d'Ariane et l'indicateur d'étapes.
      </p>

      <div class="doc-bloc">
        <h3>Fil d'Ariane <code>.fs-ariane</code></h3>
        <p class="doc-bloc-note">
          Le fil d'Ariane rend visible la profondeur du service, que le guide limite
          à trois niveaux sous l'accueil (§ 5.2.2)&nbsp;: au-delà, l'usager se perd. Un
          fil qui dépasse quatre éléments signale une arborescence à resserrer, pas un
          fil à raccourcir.
        </p>
        <div class="doc-demo" data-libelle="Fil d'Ariane">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <nav class="fs-ariane" aria-label="Fil d'Ariane">
              <ol>
                <li><a href="#">Accueil</a></li>
                <li><a href="#">État civil</a></li>
                <li><a href="#">Naissance</a></li>
                <li><span aria-current="page">Demander un acte de naissance</span></li>
              </ol>
            </nav>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Onglets <code>[role="tablist"]</code></h3>
        <p class="doc-bloc-note">
          Au clavier, les flèches changent d'onglet, Début et Fin atteignent les
          extrémités. La tabulation ne s'arrête que sur l'onglet actif, puis passe
          directement au panneau affiché.
        </p>
        <div class="doc-demo" data-libelle="Onglets">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div>
              <div class="fs-onglets" role="tablist" aria-label="Informations sur la démarche">
                <button class="fs-onglet" role="tab" id="o1" aria-controls="p1" aria-selected="true">Procédure</button>
                <button class="fs-onglet" role="tab" id="o2" aria-controls="p2" aria-selected="false">Pièces à fournir</button>
                <button class="fs-onglet" role="tab" id="o3" aria-controls="p3" aria-selected="false">Coût et délai</button>
                <button class="fs-onglet" role="tab" id="o4" aria-controls="p4" aria-selected="false">Contact</button>
              </div>
              <div class="fs-panneau" role="tabpanel" id="p1" aria-labelledby="o1" tabindex="0">
                <p>La demande se dépose en ligne ou au guichet de la mairie du lieu de naissance. Le dépôt en ligne nécessite un compte usager vérifié.</p>
              </div>
              <div class="fs-panneau" role="tabpanel" id="p2" aria-labelledby="o2" tabindex="0" hidden>
                <p>Copie de la pièce d'identité du demandeur, justificatif de filiation, et procuration le cas échéant.</p>
              </div>
              <div class="fs-panneau" role="tabpanel" id="p3" aria-labelledby="o3" tabindex="0" hidden>
                <p>Droit de timbre de 500&nbsp;F&nbsp;CFA. Délai indicatif de traitement. Trois jours ouvrés à compter de la validation du dossier.</p>
              </div>
              <div class="fs-panneau" role="tabpanel" id="p4" aria-labelledby="o4" tabindex="0" hidden>
                <p>Direction générale de la modernisation de l'état civil&nbsp;— Ouagadougou. Permanence téléphonique du lundi au vendredi.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Employer les onglets pour éviter une succession de boîtes de dialogue ou une rangée de boutons trop longue (guide, § 4.4.5).</li>
              <li>Une seule rangée, languettes alignées à gauche et accolées, libellés centrés sur une ligne&nbsp;: c'est le rendu par défaut de <code>.fs-onglets</code>. Sur écran étroit, la rangée défile horizontalement au lieu de passer à la ligne.</li>
              <li>Onglets liés, qui forment ensemble une seule saisie&nbsp;: les boutons «&nbsp;Valider&nbsp;» et «&nbsp;Annuler&nbsp;» sont placés une seule fois, sous les panneaux, en dehors des onglets.</li>
              <li>Onglets indépendants, chacun enregistré à part&nbsp;: chaque panneau porte ses propres boutons, «&nbsp;Annuler&nbsp;» devient «&nbsp;Fermer&nbsp;» et le bouton de validation devient facultatif.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Deux rangées d'onglets ou plus.</li>
              <li>Un libellé d'onglet sur deux lignes, ou tronqué&nbsp;: on le raccourcit en choisissant un autre mot.</li>
              <li>Des boutons de validation répétés dans chaque panneau d'onglets liés&nbsp;: l'usager ne sait plus ce qu'il valide.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Étapes <code>.fs-etapes</code></h3>
        <div class="doc-demo" data-libelle="Indicateur d'étapes">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <nav aria-label="Progression de la demande">
              <p class="fs-invisible">Étape 3 sur 5&nbsp;: pièces justificatives</p>
              <ol class="fs-etapes">
                <li class="fs-etape" data-etat="faite">
                  <span class="fs-etape-num">Étape 1</span>
                  <span class="fs-etape-titre">Identification</span>
                </li>
                <li class="fs-etape" data-etat="faite">
                  <span class="fs-etape-num">Étape 2</span>
                  <span class="fs-etape-titre">Objet de la demande</span>
                </li>
                <li class="fs-etape" data-etat="courante" aria-current="step">
                  <span class="fs-etape-num">Étape 3</span>
                  <span class="fs-etape-titre">Pièces justificatives</span>
                </li>
                <li class="fs-etape">
                  <span class="fs-etape-num">Étape 4</span>
                  <span class="fs-etape-titre">Paiement</span>
                </li>
                <li class="fs-etape">
                  <span class="fs-etape-num">Étape 5</span>
                  <span class="fs-etape-titre">Récapitulatif</span>
                </li>
              </ol>
            </nav>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Pagination <code>.fs-pagination</code></h3>
        <div class="doc-demo" data-libelle="Pagination">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <nav class="fs-pagination" aria-label="Pagination des résultats">
              <a href="#" aria-label="Page précédente"><span class="fs-icone fs-icone--chevron-gauche" aria-hidden="true"></span></a>
              <a href="#">1</a>
              <a href="#" aria-current="page">2</a>
              <a href="#">3</a>
              <a href="#">4</a>
              <span class="fs-ellipse" aria-hidden="true">…</span>
              <a href="#">18</a>
              <a href="#" aria-label="Page suivante"><span class="fs-icone fs-icone--chevron-droite" aria-hidden="true"></span></a>
            </nav>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Navigation principale et barre de service</h3>
        <div class="doc-demo" data-libelle="En-tête complet">
          <div class="doc-demo-scene doc-demo-scene--pile" style="padding:0;">
            <div>
              <div class="fs-barre-service">
                <div class="fs-conteneur">
                  <span>Ministère de l'Économie et des Finances</span>
                  <a href="#">Nous contacter</a>
                </div>
              </div>
              <div class="fs-filet"></div>
              <nav class="fs-nav" aria-label="Exemple de navigation">
                <div class="fs-conteneur">
                  <ul class="fs-nav-liste">
                    <li><a class="fs-nav-lien" href="#" aria-current="page">Démarches</a></li>
                    <li><a class="fs-nav-lien" href="#">Mon dossier</a></li>
                    <li><a class="fs-nav-lien" href="#">Paiements</a></li>
                    <li><a class="fs-nav-lien" href="#">Aide</a></li>
                  </ul>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc" data-regles>
        <h3>Déplacement du focus dans une page</h3>
        <p class="doc-bloc-note">
          Les règles du guide sur la navigation au clavier (§ 5.1), transposées au
          web. Aucune n'exige de script&nbsp;: elles tiennent à l'ordre du balisage.
        </p>
        <ul class="fs-liste-puces">
          <li>La tabulation suit l'ordre de lecture, de gauche à droite et de haut en bas&nbsp;: l'ordre du code source est l'ordre visuel, sans <code>tabindex</code> positif.</li>
          <li>Le focus s'arrête sur les champs de saisie, les listes et les commandes, jamais sur une donnée seulement affichée (§ 5.1.1).</li>
          <li>À l'ouverture d'un formulaire, le focus peut être placé sur le champ que l'usager a le plus de chances de remplir en premier (§ 5.1.3).</li>
          <li>Un champ de longueur fixe passe au suivant quand il est complet (§ 5.1.3)&nbsp;: c'est le cas des cases de <code>.fs-code</code> et des trois champs de <code>.fs-date</code>.</li>
          <li>La touche Entrée n'imite pas la tabulation, contrairement à ce que prévoit le § 5.1.2&nbsp;: sur le web, elle soumet le formulaire, et lui donner un autre sens désoriente les usagers du clavier et des lecteurs d'écran.</li>
          <li>Le déplacement en boucle de la tabulation n'est appliqué que dans une fenêtre modale, où le focus reste enfermé&nbsp;; ailleurs, le navigateur laisse sortir de la page.</li>
        </ul>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="information">
      <h2>Information</h2>
      <p class="doc-section-intro">
        Quand tout est présenté avec la même urgence, plus rien ne ressort. Les
        encarts distinguent quatre registres, du simple repère documentaire à
        l'alerte critique.
      </p>

      <div class="doc-bloc">
        <h3>Encarts <code>.fs-encart</code></h3>
        <div class="doc-demo" data-libelle="Encarts">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-encart fs-encart--info">
              <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
              <div class="fs-encart-corps">
                <p class="fs-encart-titre">Pour information</p>
                <p>Les guichets sont fermés les jours fériés. Le dépôt en ligne reste disponible sans interruption.</p>
              </div>
            </div>

            <div class="fs-encart fs-encart--succes">
              <span class="fs-encart-marque fs-icone fs-icone--succes" aria-hidden="true"></span>
              <div class="fs-encart-corps">
                <p class="fs-encart-titre">Demande enregistrée</p>
                <p>Votre dossier porte la référence BF-2026-004871-OUA. Un récépissé a été envoyé à votre adresse électronique.</p>
              </div>
            </div>

            <div class="fs-encart fs-encart--alerte">
              <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
              <div class="fs-encart-corps">
                <p class="fs-encart-titre">Pièce complémentaire attendue</p>
                <p>Un justificatif de domicile de moins de trois mois doit être transmis avant le 30 septembre 2026, faute de quoi le dossier sera classé sans suite.</p>
              </div>
            </div>

            <div class="fs-encart fs-encart--danger" role="alert">
              <span class="fs-encart-marque fs-icone fs-icone--erreur" aria-hidden="true"></span>
              <div class="fs-encart-corps">
                <p class="fs-encart-titre">Demande rejetée</p>
                <p>La pièce d'identité transmise est illisible. Déposez une nouvelle copie pour relancer l'instruction du dossier.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Encart refermable <code>.fs-encart-fermer</code></h3>
        <p class="doc-bloc-note">
          Un encart ne devient refermable que si sa disparition ne fait perdre aucune
          information nécessaire à la tâche en cours. Une erreur de saisie, une
          échéance et un avertissement de fraude ne se ferment jamais&nbsp;: l'usager les
          écarterait d'un geste et poursuivrait sans les avoir lues.
        </p>

        <div class="doc-demo" data-libelle="Encart refermable">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-encart fs-encart--info" role="status">
              <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
              <div class="fs-encart-corps">
                <p class="fs-encart-titre">Nouveau service</p>
                <p>Le certificat de résidence peut désormais être demandé en ligne dans toutes les communes de la région du Centre.</p>
              </div>
              <button type="button" class="fs-encart-fermer" data-ferme-encart aria-label="Fermer ce message">
                <span class="fs-icone fs-icone--fermer" aria-hidden="true"></span>
              </button>
            </div>

            <div class="fs-encart fs-encart--danger" role="alert">
              <span class="fs-encart-marque fs-icone fs-icone--erreur" aria-hidden="true"></span>
              <div class="fs-encart-corps">
                <p class="fs-encart-titre">Demande rejetée</p>
                <p>Cet encart n'a pas de commande de fermeture. Le motif du rejet et la voie de recours doivent rester visibles.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          À la fermeture, le focus est repris explicitement sur l'élément suivant.
          Sans cela, un usager au clavier se retrouve renvoyé en début de document,
          l'élément qui portait son focus ayant disparu.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Bandeau d'urgence <code>.fs-bandeau</code></h3>
        <p class="doc-bloc-note">
          Réservé aux communications de portée nationale. Employé pour une
          information de service courante, il perdrait sa valeur d'alerte.
        </p>
        <div class="doc-demo" data-libelle="Bandeau">
          <div class="doc-demo-scene doc-demo-scene--pile" style="padding:0;">
            <div class="fs-bandeau" role="alert">
              <div class="fs-conteneur">
                <span class="fs-bandeau-etiquette">Alerte</span>
                <span>Interruption programmée des téléservices le dimanche 27 septembre de 0&nbsp;h à 6&nbsp;h.</span>
                <a href="#">En savoir plus</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Étiquettes, pastilles et compteurs</h3>
        <div class="doc-demo" data-libelle="Étiquettes">
          <div class="doc-demo-scene">
            <span class="fs-badge fs-badge--neutre">Brouillon</span>
            <span class="fs-badge fs-badge--info">En cours d'instruction</span>
            <span class="fs-badge fs-badge--succes">Validé</span>
            <span class="fs-badge fs-badge--alerte">Complément demandé</span>
            <span class="fs-badge fs-badge--danger">Rejeté</span>
            <span class="fs-badge fs-badge--relief">Nouveau</span>

            <span class="fs-etiquette">Ouagadougou <button type="button" aria-label="Retirer le filtre Ouagadougou"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button></span>

            <span class="fs-statut fs-statut--cours">En cours d'instruction</span>
            <span class="fs-statut fs-statut--succes">Disponible au retrait</span>

            <span class="fs-compteur">4</span>
          </div>
        </div>
        <div class="doc-note fs-mt-4">
          La pastille d'état <code>.fs-statut</code> associe un point coloré à un
          libellé explicite. Un usager daltonien ou un écran monochrome lisent la
          même information.
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Avatars</h3>
        <div class="doc-demo" data-libelle="Avatars">
          <div class="doc-demo-scene">
            <span class="fs-avatar fs-avatar--sm">ao</span>
            <span class="fs-avatar">ao</span>
            <span class="fs-avatar fs-avatar--lg">ao</span>
            <div class="fs-avatars">
              <span class="fs-avatar" title="A. Ouédraogo">ao</span>
              <span class="fs-avatar" title="S. Kaboré">sk</span>
              <span class="fs-avatar" title="M. Traoré">mt</span>
              <span class="fs-avatar" title="3 autres agents">+3</span>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Cartes <code>.fs-carte</code></h3>
        <div class="doc-demo" data-libelle="Cartes">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-auto-grille" style="--min-colonne: 260px;">
              <article class="fs-carte fs-carte--cliquable">
                <div class="fs-carte-corps">
                  <span class="fs-badge fs-badge--relief" style="align-self:flex-start;">Actualité</span>
                  <h4 class="fs-carte-titre"><a href="#">Ouverture du téléservice de délivrance des actes d'état civil</a></h4>
                  <p class="fs-carte-texte">Les communes de la région du Centre basculent sur la plateforme nationale à compter du 1er octobre 2026.</p>
                </div>
                <div class="fs-carte-pied">
                  <p class="fs-legende">Publié le 18 septembre 2026</p>
                </div>
              </article>

              <article class="fs-carte fs-carte--marquee">
                <div class="fs-carte-corps">
                  <h4 class="fs-carte-titre">Communiqué officiel</h4>
                  <p class="fs-carte-texte">Le filet tricolore en tête de carte signale un contenu émanant directement du Gouvernement.</p>
                </div>
              </article>

              <div class="fs-stat">
                <span class="fs-stat-valeur">128&nbsp;400</span>
                <span class="fs-stat-libelle">demandes traitées depuis janvier</span>
                <span class="fs-stat-variation fs-stat-variation--hausse">+12&nbsp;% sur un an</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Listes</h3>
        <div class="doc-demo" data-libelle="Listes">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <ul class="fs-liste-puces">
              <li>Pièce d'identité en cours de validité</li>
              <li>Justificatif de domicile de moins de trois mois</li>
              <li>Quittance de paiement du droit de timbre</li>
            </ul>

            <ul class="fs-liste-controle">
              <li>Le dossier a été transmis au service instructeur</li>
              <li>Le paiement a été encaissé</li>
              <li>Le récépissé a été adressé au demandeur</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Infobulle <code>.fs-infobulle</code></h3>
        <p class="doc-bloc-note">
          C'est l'aide contextuelle rapide du guide (§ 8.2.2)&nbsp;: elle apparaît
          quand le pointeur survole l'élément ou quand celui-ci reçoit le focus, et
          disparaît d'elle-même dès qu'il les quitte, sans commande de fermeture. Elle
          décrit brièvement le champ ou le bouton&nbsp;: définition, contenu, format
          attendu. Elle ne porte jamais une information indispensable&nbsp;: sur écran
          tactile, elle peut rester inaccessible.
        </p>
        <div class="doc-demo" data-libelle="Infobulle">
          <div class="doc-demo-scene">
            <span class="fs-infobulle">
              <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" aria-describedby="ib1">Droit de timbre</button>
              <span class="fs-infobulle-contenu" role="tooltip" id="ib1">
                Taxe forfaitaire perçue au profit du Trésor public lors de la délivrance d'un document administratif.
              </span>
            </span>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Citation</h3>
        <div class="doc-demo" data-libelle="Citation">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <blockquote>
              Tout usager a le droit d'obtenir communication des documents administratifs
              qui le concernent, dans les conditions fixées par la loi.
              <cite>Article 4 de la loi relative à l'accès à l'information publique</cite>
            </blockquote>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="donnees">
      <h2>Données</h2>
      <p class="doc-section-intro">
        Les tableaux portent l'essentiel des données administratives. Ils doivent
        rester lisibles sur un écran de 320&nbsp;px de large, ce qui suppose une
        stratégie d'adaptation décidée dès la conception, pas ajoutée ensuite.
      </p>

      <div class="doc-bloc">
        <h3>Tableau <code>.fs-tableau</code></h3>
        <p class="doc-bloc-note">
          Tri côté client activé par <code>data-triable</code>. L'attribut
          <code>data-valeur</code> permet de trier sur une donnée brute différente de
          l'affichage, par exemple une date au format ISO. Sur écran étroit,
          <code>.fs-tableau--empile</code> transforme chaque ligne en fiche.
        </p>
        <p class="doc-bloc-note">
          Tout tableau porte un titre, dans son <code>&lt;caption&gt;</code> (guide,
          § 4.4.3)&nbsp;: c'est lui qui permet de se repérer d'un coup d'œil, et que le
          lecteur d'écran annonce en entrant dans le tableau. Les libellés sont
          alignés à gauche, les nombres et montants à droite avec
          <code>.fs-num</code>&nbsp;; aucune colonne n'est centrée, ce qui dessinerait des
          vagues verticales. Les lignes suivent l'ordre de travail de l'usager, à
          défaut l'ordre alphabétique ou numérique.
        </p>
        <div class="doc-demo" data-libelle="Tableau triable">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-tableau-cadre">
              <table class="fs-tableau fs-tableau--empile" data-triable>
                <caption>Demandes déposées au cours des trente derniers jours</caption>
                <thead>
                  <tr>
                    <th aria-sort="none"><button type="button">Référence</button></th>
                    <th aria-sort="none"><button type="button">Démarche</button></th>
                    <th aria-sort="none"><button type="button">Dépôt</button></th>
                    <th aria-sort="none"><button type="button">Montant</button></th>
                    <th>État</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td data-colonne="Référence"><code>BF-2026-004871</code></td>
                    <td data-colonne="Démarche">Acte de naissance</td>
                    <td data-colonne="Dépôt" data-valeur="2026-09-18">18/09/2026</td>
                    <td data-colonne="Montant" class="fs-num" data-valeur="500">500&nbsp;F</td>
                    <td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td>
                  </tr>
                  <tr>
                    <td data-colonne="Référence"><code>BF-2026-004902</code></td>
                    <td data-colonne="Démarche">Certificat de résidence</td>
                    <td data-colonne="Dépôt" data-valeur="2026-09-15">15/09/2026</td>
                    <td data-colonne="Montant" class="fs-num" data-valeur="1000">1&nbsp;000&nbsp;F</td>
                    <td data-colonne="État"><span class="fs-statut fs-statut--cours">En cours d'instruction</span></td>
                  </tr>
                  <tr>
                    <td data-colonne="Référence"><code>BF-2026-004915</code></td>
                    <td data-colonne="Démarche">Casier judiciaire</td>
                    <td data-colonne="Dépôt" data-valeur="2026-09-12">12/09/2026</td>
                    <td data-colonne="Montant" class="fs-num" data-valeur="2000">2&nbsp;000&nbsp;F</td>
                    <td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td>
                  </tr>
                  <tr>
                    <td data-colonne="Référence"><code>BF-2026-004930</code></td>
                    <td data-colonne="Démarche">Passeport ordinaire</td>
                    <td data-colonne="Dépôt" data-valeur="2026-09-02">02/09/2026</td>
                    <td data-colonne="Montant" class="fs-num" data-valeur="50000">50&nbsp;000&nbsp;F</td>
                    <td data-colonne="État"><span class="fs-statut fs-statut--rejet">Rejeté</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Tableau défilant <code>.fs-tableau-cadre--defilant</code></h3>
        <p class="doc-bloc-note">
          Pour un tableau trop grand pour être vu en entier. Le cadre limite sa
          hauteur et fait défiler le tableau dans les deux sens&nbsp;; pendant le
          défilement vertical, les en-têtes de colonnes restent visibles, et pendant
          le défilement horizontal, ce sont les en-têtes de lignes, à condition
          qu'ils soient balisés <code>&lt;th scope="row"&gt;</code> (guide, § 4.4.3).
          La hauteur par défaut est de <code>min(70vh, 40rem)</code>&nbsp;; la démonstration
          la réduit pour faire apparaître le défilement.
        </p>
        <div class="doc-demo" data-libelle="Tableau défilant">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-tableau-cadre fs-tableau-cadre--defilant" style="max-height: 20rem;" tabindex="0" role="region" aria-labelledby="tableau-defilant-titre">
              <table class="fs-tableau fs-tableau--compact" style="min-width: 60rem;">
                <caption id="tableau-defilant-titre">Demandes reçues par démarche et par mois, 2026 (données d'exemple)</caption>
                <thead>
                  <tr><th scope="col">Démarche</th><th scope="col" class="fs-num">Janvier</th><th scope="col" class="fs-num">Février</th><th scope="col" class="fs-num">Mars</th><th scope="col" class="fs-num">Avril</th><th scope="col" class="fs-num">Mai</th><th scope="col" class="fs-num">Juin</th><th scope="col" class="fs-num">Juillet</th><th scope="col" class="fs-num">Août</th><th scope="col" class="fs-num">Septembre</th><th scope="col" class="fs-num">Total</th></tr>
                </thead>
                <tbody>
                    <tr><th scope="row">Acte de décès</th><td class="fs-num">1&nbsp;994</td><td class="fs-num">1&nbsp;589</td><td class="fs-num">1&nbsp;336</td><td class="fs-num">1&nbsp;901</td><td class="fs-num">1&nbsp;369</td><td class="fs-num">1&nbsp;727</td><td class="fs-num">1&nbsp;966</td><td class="fs-num">1&nbsp;457</td><td class="fs-num">1&nbsp;363</td><td class="fs-num">14&nbsp;702</td></tr>
                    <tr><th scope="row">Acte de mariage</th><td class="fs-num">1&nbsp;672</td><td class="fs-num">1&nbsp;691</td><td class="fs-num">1&nbsp;993</td><td class="fs-num">2&nbsp;358</td><td class="fs-num">1&nbsp;721</td><td class="fs-num">1&nbsp;811</td><td class="fs-num">2&nbsp;177</td><td class="fs-num">2&nbsp;467</td><td class="fs-num">2&nbsp;132</td><td class="fs-num">18&nbsp;022</td></tr>
                    <tr><th scope="row">Acte de naissance</th><td class="fs-num">1&nbsp;582</td><td class="fs-num">1&nbsp;730</td><td class="fs-num">2&nbsp;021</td><td class="fs-num">1&nbsp;654</td><td class="fs-num">1&nbsp;902</td><td class="fs-num">2&nbsp;007</td><td class="fs-num">2&nbsp;033</td><td class="fs-num">2&nbsp;024</td><td class="fs-num">2&nbsp;129</td><td class="fs-num">17&nbsp;082</td></tr>
                    <tr><th scope="row">Attestation de résidence</th><td class="fs-num">766</td><td class="fs-num">785</td><td class="fs-num">698</td><td class="fs-num">755</td><td class="fs-num">598</td><td class="fs-num">596</td><td class="fs-num">644</td><td class="fs-num">798</td><td class="fs-num">716</td><td class="fs-num">6&nbsp;356</td></tr>
                    <tr><th scope="row">Carte grise</th><td class="fs-num">1&nbsp;601</td><td class="fs-num">1&nbsp;927</td><td class="fs-num">1&nbsp;526</td><td class="fs-num">1&nbsp;446</td><td class="fs-num">1&nbsp;397</td><td class="fs-num">1&nbsp;825</td><td class="fs-num">1&nbsp;327</td><td class="fs-num">1&nbsp;483</td><td class="fs-num">1&nbsp;622</td><td class="fs-num">14&nbsp;154</td></tr>
                    <tr><th scope="row">Casier judiciaire</th><td class="fs-num">1&nbsp;924</td><td class="fs-num">1&nbsp;585</td><td class="fs-num">2&nbsp;117</td><td class="fs-num">1&nbsp;455</td><td class="fs-num">1&nbsp;685</td><td class="fs-num">1&nbsp;946</td><td class="fs-num">1&nbsp;481</td><td class="fs-num">1&nbsp;740</td><td class="fs-num">1&nbsp;394</td><td class="fs-num">15&nbsp;327</td></tr>
                    <tr><th scope="row">Certificat de résidence</th><td class="fs-num">2&nbsp;535</td><td class="fs-num">3&nbsp;192</td><td class="fs-num">3&nbsp;508</td><td class="fs-num">3&nbsp;548</td><td class="fs-num">2&nbsp;894</td><td class="fs-num">2&nbsp;908</td><td class="fs-num">3&nbsp;108</td><td class="fs-num">3&nbsp;518</td><td class="fs-num">2&nbsp;523</td><td class="fs-num">27&nbsp;734</td></tr>
                    <tr><th scope="row">Certificat de vie</th><td class="fs-num">836</td><td class="fs-num">692</td><td class="fs-num">750</td><td class="fs-num">565</td><td class="fs-num">762</td><td class="fs-num">745</td><td class="fs-num">851</td><td class="fs-num">799</td><td class="fs-num">633</td><td class="fs-num">6&nbsp;633</td></tr>
                    <tr><th scope="row">Extrait de casier</th><td class="fs-num">2&nbsp;254</td><td class="fs-num">1&nbsp;797</td><td class="fs-num">2&nbsp;299</td><td class="fs-num">1&nbsp;804</td><td class="fs-num">2&nbsp;020</td><td class="fs-num">1&nbsp;921</td><td class="fs-num">1&nbsp;688</td><td class="fs-num">1&nbsp;747</td><td class="fs-num">2&nbsp;128</td><td class="fs-num">17&nbsp;658</td></tr>
                    <tr><th scope="row">Légalisation de signature</th><td class="fs-num">1&nbsp;882</td><td class="fs-num">2&nbsp;299</td><td class="fs-num">1&nbsp;613</td><td class="fs-num">1&nbsp;933</td><td class="fs-num">2&nbsp;020</td><td class="fs-num">2&nbsp;310</td><td class="fs-num">2&nbsp;254</td><td class="fs-num">2&nbsp;293</td><td class="fs-num">1&nbsp;784</td><td class="fs-num">18&nbsp;388</td></tr>
                    <tr><th scope="row">Passeport ordinaire</th><td class="fs-num">2&nbsp;489</td><td class="fs-num">2&nbsp;215</td><td class="fs-num">1&nbsp;943</td><td class="fs-num">1&nbsp;808</td><td class="fs-num">1&nbsp;675</td><td class="fs-num">1&nbsp;737</td><td class="fs-num">2&nbsp;193</td><td class="fs-num">1&nbsp;611</td><td class="fs-num">2&nbsp;349</td><td class="fs-num">18&nbsp;020</td></tr>
                    <tr><th scope="row">Permis de conduire</th><td class="fs-num">960</td><td class="fs-num">838</td><td class="fs-num">1&nbsp;033</td><td class="fs-num">1&nbsp;010</td><td class="fs-num">1&nbsp;103</td><td class="fs-num">1&nbsp;285</td><td class="fs-num">1&nbsp;161</td><td class="fs-num">1&nbsp;079</td><td class="fs-num">1&nbsp;127</td><td class="fs-num">9&nbsp;596</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          Le cadre reçoit le focus (<code>tabindex="0"</code>) et porte le titre du
          tableau&nbsp;: un usager au clavier peut ainsi le faire défiler avec les
          flèches.
        </p>
      </div>

      <div class="doc-bloc">
        <h3>Tableau paginé <code>.fs-tableau-bloc</code></h3>
        <p class="doc-bloc-note">
          Le motif complet&nbsp;: barre d'outils avec le nombre de résultats et le choix
          du nombre de lignes, tableau triable, puis pied portant l'intervalle affiché
          et la pagination. Les deux barres restent fixes pendant que le tableau défile
          horizontalement, sans quoi la pagination s'échapperait hors de l'écran sur
          un tableau large.
        </p>

        <div class="doc-demo" data-libelle="Tableau filtré et paginé">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-tableau-bloc" data-pagination="5" data-filtres data-export="demandes">

              <div class="fs-impression-entete">
                <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="">
                <div>
                  <p class="fs-impression-titre">Demandes déposées</p>
                  <p class="fs-impression-meta">Direction générale de la modernisation de l'état civil</p>
                  <p class="fs-impression-meta" data-impression-contexte></p>
                </div>
              </div>

              <div class="fs-filtres">
                <div class="fs-champ">
                  <label class="fs-label" for="f-recherche">Rechercher</label>
                  <input class="fs-saisie" type="search" id="f-recherche"
                         placeholder="Référence, démarche…" data-filtre="recherche">
                </div>
                <div class="fs-champ">
                  <label class="fs-label" for="f-etat">État</label>
                  <select class="fs-select" id="f-etat" data-filtre="egal" data-filtre-colonne="État">
                    <option value="">Tous</option>
                    <option>Validé</option>
                    <option>En cours d'instruction</option>
                    <option>Complément demandé</option>
                    <option>Rejeté</option>
                    <option>Disponible au retrait</option>
                  </select>
                </div>
                <div class="fs-champ">
                  <label class="fs-label" for="f-depuis">Déposé à partir du</label>
                  <input class="fs-saisie" type="date" id="f-depuis"
                         data-filtre="min" data-filtre-colonne="Dépôt">
                </div>
                <div class="fs-champ">
                  <label class="fs-label" for="f-montant">Montant minimum</label>
                  <input class="fs-saisie" type="number" id="f-montant" min="0" step="500"
                         placeholder="0" data-filtre="min" data-filtre-colonne="Montant">
                </div>
              </div>

              <div class="fs-filtres-actifs" hidden></div>

              <div class="fs-tableau-outils">
                <p class="fs-tableau-compte" style="margin:0;">
                  <b>0</b> demandes
                </p>
                <div class="fs-rangee">
                  <div class="fs-tableau-export">
                    <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" data-export-csv>
                      <span class="fs-icone fs-icone--telecharger" aria-hidden="true"></span>
                      CSV
                    </button>
                    <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" data-export-impression>
                      <span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span>
                      Imprimer ou PDF
                    </button>
                  </div>
                  <label class="fs-tableau-taille">
                    Lignes par page
                    <select class="fs-select">
                      <option>5</option>
                      <option>10</option>
                      <option>25</option>
                    </select>
                  </label>
                </div>
              </div>

              <div class="fs-tableau-cadre">
                <table class="fs-tableau fs-tableau--empile" data-triable>
                  <caption>Demandes déposées</caption>
                  <thead>
                    <tr>
                      <th aria-sort="none"><button type="button">Référence</button></th>
                      <th aria-sort="none"><button type="button">Démarche</button></th>
                      <th aria-sort="none"><button type="button">Dépôt</button></th>
                      <th aria-sort="none"><button type="button">Montant</button></th>
                      <th>État</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td data-colonne="Référence"><code>BF-2026-004871</code></td><td data-colonne="Démarche">Acte de naissance</td><td data-colonne="Dépôt" data-valeur="2026-09-18">18/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004902</code></td><td data-colonne="Démarche">Certificat de résidence</td><td data-colonne="Dépôt" data-valeur="2026-09-15">15/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="1000">1 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--cours">En cours d'instruction</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004915</code></td><td data-colonne="Démarche">Casier judiciaire</td><td data-colonne="Dépôt" data-valeur="2026-09-12">12/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="2000">2 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004930</code></td><td data-colonne="Démarche">Passeport ordinaire</td><td data-colonne="Dépôt" data-valeur="2026-09-02">02/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="50000">50 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--rejet">Rejeté</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004944</code></td><td data-colonne="Démarche">Acte de mariage</td><td data-colonne="Dépôt" data-valeur="2026-08-28">28/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004951</code></td><td data-colonne="Démarche">Certificat de vie</td><td data-colonne="Dépôt" data-valeur="2026-08-24">24/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="0">Gratuit</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Disponible au retrait</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004968</code></td><td data-colonne="Démarche">Carte grise</td><td data-colonne="Dépôt" data-valeur="2026-08-19">19/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="15000">15 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--cours">En cours d'instruction</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004977</code></td><td data-colonne="Démarche">Légalisation de signature</td><td data-colonne="Dépôt" data-valeur="2026-08-14">14/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="200">200 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004983</code></td><td data-colonne="Démarche">Attestation de résidence</td><td data-colonne="Dépôt" data-valeur="2026-08-08">08/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="1000">1 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-004990</code></td><td data-colonne="Démarche">Acte de décès</td><td data-colonne="Dépôt" data-valeur="2026-08-03">03/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-005002</code></td><td data-colonne="Démarche">Extrait de casier</td><td data-colonne="Dépôt" data-valeur="2026-07-30">30/07/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="2000">2 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Disponible au retrait</span></td></tr>
                    <tr><td data-colonne="Référence"><code>BF-2026-005014</code></td><td data-colonne="Démarche">Permis de conduire</td><td data-colonne="Dépôt" data-valeur="2026-07-22">22/07/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="25000">25 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--rejet">Rejeté</span></td></tr>
                  </tbody>
                </table>
              </div>

              <div class="fs-tableau-vide" hidden>
                <p class="fs-tableau-vide-titre"></p>
                <p data-vide-texte></p>
              </div>

              <div class="fs-tableau-pied">
                <p class="fs-tableau-intervalle" style="margin:0;"></p>
                <nav class="fs-pagination" aria-label="Pagination des demandes"></nav>
              </div>

            </div>
          </div>
        </div>

        <div class="doc-note fs-mt-6">
          Les commandes de pagination sont <strong>construites par le script</strong>,
          jamais écrites dans le balisage. Sans JavaScript, aucune commande n'apparaît
          et le tableau s'affiche en entier&nbsp;: l'usager voit toutes les données, ce qui
          est la seule dégradation acceptable. Un pied contenant des liens inertes
          serait pire que pas de pagination du tout.
        </div>

        <h4 class="fs-mt-8">Déclarer un filtre</h4>
        <p class="doc-bloc-note">
          Un contrôle indique ce qu'il filtre par <code>data-filtre</code>, et sur
          quelle colonne par <code>data-filtre-colonne</code>. La colonne est désignée
          par son intitulé, celui que les cellules portent déjà dans
          <code>data-colonne</code> pour le mode empilé. Le même attribut sert donc à
          la lisibilité sur mobile et au filtrage, sans rien ajouter au balisage.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Valeurs de l'attribut <code>data-filtre</code></caption>
            <thead>
              <tr><th>Valeur</th><th>Effet</th><th>Contrôle adapté</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><code>recherche</code></td>
                <td>Cherche dans toute la ligne, accents ignorés</td>
                <td><code>type="search"</code></td>
              </tr>
              <tr>
                <td><code>egal</code></td>
                <td>Correspondance exacte sur une colonne</td>
                <td><code>&lt;select&gt;</code></td>
              </tr>
              <tr>
                <td><code>min</code></td>
                <td>Valeur supérieure ou égale</td>
                <td><code>type="date"</code>, <code>type="number"</code></td>
              </tr>
              <tr>
                <td><code>max</code></td>
                <td>Valeur inférieure ou égale</td>
                <td><code>type="date"</code>, <code>type="number"</code></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-note fs-mt-6">
          Comme pour le tri, <code>data-valeur</code> porte la donnée brute quand
          l'affichage en diffère&nbsp;: <code>data-valeur="2026-09-18"</code> pour
          « 18/09/2026 », <code>data-valeur="0"</code> pour « Gratuit ». Les dates ISO
          sont comparées comme du texte, ce qui donne le bon ordre&nbsp;; les traiter
          comme des nombres les tronquerait à l'année.
        </div>

        <h4 class="fs-mt-8">Filtre et pagination</h4>
        <p class="doc-bloc-note">
          Deux couches distinctes&nbsp;: le filtre
          <strong>écarte</strong> une ligne du jeu de données, la pagination
          <strong>masque</strong> celles qui ne sont pas sur la page courante. Une
          ligne écartée ne compte dans aucun total et n'occupe aucune place sur une
          page. Tout changement de filtre renvoie à la première page.
        </p>

        <h4 class="fs-mt-8">Extraction</h4>
        <p class="doc-bloc-note">
          Le <strong>CSV</strong> sert à exploiter les données dans un tableur&nbsp;;
          l'<strong>impression</strong> produit un document
          paginé, destiné à être archivé ou transmis, que tout système d'exploitation
          sait enregistrer en PDF.
        </p>

        <div class="doc-note">
          <strong>Pourquoi pas un PDF fabriqué par la page&nbsp;?</strong> Les
          bibliothèques qui le permettent pèsent plusieurs centaines de kilo-octets,
          soit davantage que l'ensemble de ce système, pour un résultat inférieur à ce
          que le navigateur sait déjà faire&nbsp;: pagination, en-tête de colonnes répété,
          polices intégrées. Lorsqu'un PDF doit être <em>joint</em> à un envoi
          (récépissé, quittance, décision), il est produit par le serveur, qui seul
          peut le signer et lui donner valeur probante.
        </div>

        <h4 class="fs-mt-8">Règles d'extraction</h4>
        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Règles appliquées à l'extraction</caption>
            <thead>
              <tr><th>Point</th><th>Règle</th><th>Motif</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Étendue</td>
                <td>Toutes les lignes retenues par les filtres, pas la page affichée</td>
                <td>Une extraction limitée à la page 1 sur 12 est un piège.</td>
              </tr>
              <tr>
                <td>Valeurs</td>
                <td><code>data-valeur</code> prime sur l'affichage</td>
                <td>Une date part en ISO et un montant en nombre, exploitables dans un tableur.</td>
              </tr>
              <tr>
                <td>Séparateur</td>
                <td>Point-virgule</td>
                <td>C'est celui qu'attend un tableur configuré en français, où la virgule est le séparateur décimal.</td>
              </tr>
              <tr>
                <td>Encodage</td>
                <td>UTF-8 avec indicateur d'ordre des octets</td>
                <td>Sans lui, un tableur lit le fichier en encodage local et les accents deviennent illisibles.</td>
              </tr>
              <tr>
                <td>Colonnes</td>
                <td>Celles ne portant que des commandes sont exclues par <code>data-export="non"</code></td>
                <td>Une colonne de boutons n'a aucun sens dans un fichier.</td>
              </tr>
              <tr>
                <td>Nom du fichier</td>
                <td><code>demandes-2026-09-21-filtre.csv</code></td>
                <td>La date situe l'extraction, le suffixe signale qu'elle est partielle.</td>
              </tr>
              <tr>
                <td>Contexte imprimé</td>
                <td>Emblème, service, date, nombre de lignes, filtres appliqués</td>
                <td>Une extraction qui circule hors du service doit porter son contexte, sans quoi elle n'est pas opposable.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="fs-encart fs-encart--alerte fs-mt-6">
          <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
          <div class="fs-encart-corps">
            <p class="fs-encart-titre">Données personnelles</p>
            <p>
              Une extraction sort les données du service et échappe à ses
              protections. N'exposez l'extraction qu'aux agents habilités, limitez-la
              aux colonnes nécessaires, et journalisez-la côté serveur au même titre
              qu'une consultation.
            </p>
          </div>
        </div>

        <h4 class="fs-mt-8">Où se fait la pagination</h4>
        <p class="doc-bloc-note">
          Le composant ci-dessus pagine dans le navigateur&nbsp;: toutes les lignes
          sont déjà dans la page. Cela tient jusqu'à quelques centaines de lignes&nbsp;;
          au-delà, la pagination revient au serveur.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Lieu de la pagination selon le volume</caption>
            <thead>
              <tr><th>Volume</th><th>Où paginer</th><th>Commandes</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>Moins de 100 lignes</td>
                <td>Aucune pagination&nbsp;: tout afficher</td>
                <td>—</td>
              </tr>
              <tr>
                <td>100 à 500 lignes</td>
                <td>Dans le navigateur, <code>data-pagination</code></td>
                <td><code>&lt;button&gt;</code></td>
              </tr>
              <tr>
                <td>Plus de 500 lignes</td>
                <td>Par le serveur, une adresse par page</td>
                <td><code>&lt;a href="?page=2"&gt;</code></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-usages fs-mt-8">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Afficher l'intervalle en clair&nbsp;: « Résultats 1 à 5 sur 12 ». Un numéro de page seul ne dit pas où l'on en est.</li>
              <li>Annoncer le changement de page dans une région live, le contenu changeant sans rechargement.</li>
              <li>Laisser le focus sur la commande utilisée. Le renvoyer en haut du tableau fait perdre sa place à l'usager au clavier.</li>
              <li>Revenir à la première page après un tri ou un changement de filtre.</li>
              <li>Conserver les extrémités visibles dans la fenêtre de pages&nbsp;: <code>1 … 8 [9] 10 … 18</code>.</li>
              <li>Côté serveur, donner à chaque page sa propre adresse, partageable et imprimable.</li>
              <li><strong>Rappeler les filtres appliqués</strong> sous forme d'étiquettes retirables, avec une commande « Tout effacer ».</li>
              <li>Distinguer « aucune donnée » de « aucun résultat pour ces filtres », et proposer une issue dans le second cas.</li>
              <li>Rendre le focus au contrôle après le retrait d'un filtre.</li>
              <li>Côté serveur, porter les filtres dans l'adresse pour qu'une recherche soit partageable.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li><strong>Le défilement infini.</strong> Il rend le pied de page inatteignable, interdit de partager une position, casse l'impression et oblige à tout recharger au retour en arrière.</li>
              <li><strong>Un filtre appliqué sans rappel visible.</strong> Écarter des lignes sans le dire est un piège&nbsp;: dans un contexte administratif, l'absence d'une ligne se lit comme l'absence d'un dossier.</li>
              <li>Charger toutes les lignes puis en masquer 95&nbsp;% au-delà de quelques centaines&nbsp;: le poids est transféré quand même.</li>
              <li>Désactiver le tri parce que la pagination est côté serveur&nbsp;: le tri porte alors sur l'ensemble des données, pas sur la page affichée.</li>
              <li>Afficher la pagination quand il n'y a qu'une page.</li>
              <li>Conserver un filtre d'une visite à l'autre sans le signaler.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Liste de définitions <code>.fs-definitions</code></h3>
        <p class="doc-bloc-note">
          Employée pour les fiches&nbsp;: elle exprime une relation clé-valeur sans
          recourir à un tableau, dont la sémantique supposerait deux dimensions.
        </p>
        <div class="doc-demo" data-libelle="Définitions">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <dl class="fs-definitions">
              <div><dt>Administration responsable</dt><dd>Direction générale de la modernisation de l'état civil</dd></div>
              <div><dt>Public concerné</dt><dd>Toute personne née au Burkina Faso ou de parents burkinabè</dd></div>
              <div><dt>Coût</dt><dd>500&nbsp;F&nbsp;CFA&nbsp;— droit de timbre</dd></div>
              <div><dt>Délai indicatif</dt><dd>Trois jours ouvrés à compter de la validation du dossier</dd></div>
              <div><dt>Canaux disponibles</dt><dd>En ligne, guichet de la mairie du lieu de naissance</dd></div>
            </dl>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Visualisation <code>.fs-graphe</code></h3>
        <p class="doc-bloc-note">
          Aucun graphique n'est produit par une bibliothèque tierce&nbsp;: quelques
          centaines de kilo-octets de code pour dessiner cinq barres ne se justifient
          pas sur un appareil modeste. Les formes ci-dessous sont obtenues en CSS ou
          en SVG écrit à la main, et s'accompagnent <strong>toujours</strong> du
          tableau des données, qui reste la source consultable par tous.
        </p>

        <div class="doc-demo" data-libelle="Barres horizontales">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-graphe">
              <p class="fs-graphe-titre">Demandes déposées par région</p>
              <p class="fs-graphe-note">Trente derniers jours. Source&nbsp;: plateforme des téléservices.</p>

              <div class="fs-barres" data-max="42000">
                <div class="fs-barre" data-valeur="38400">
                  <span class="fs-barre-libelle">Centre</span>
                  <span class="fs-barre-valeur">38&nbsp;400</span>
                  <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
                </div>
                <div class="fs-barre" data-valeur="21700">
                  <span class="fs-barre-libelle">Hauts-Bassins</span>
                  <span class="fs-barre-valeur">21&nbsp;700</span>
                  <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
                </div>
                <div class="fs-barre" data-valeur="12300">
                  <span class="fs-barre-libelle">Boucle du Mouhoun</span>
                  <span class="fs-barre-valeur">12&nbsp;300</span>
                  <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
                </div>
                <div class="fs-barre" data-valeur="8100">
                  <span class="fs-barre-libelle">Est</span>
                  <span class="fs-barre-valeur">8&nbsp;100</span>
                  <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
                </div>
                <div class="fs-barre" data-valeur="5400">
                  <span class="fs-barre-libelle">Sahel</span>
                  <span class="fs-barre-valeur">5&nbsp;400</span>
                  <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
                </div>
              </div>

              <details class="fs-graphe-donnees">
                <summary>Consulter les données sous forme de tableau</summary>
                <div class="fs-tableau-cadre">
                  <table class="fs-tableau fs-tableau--compact">
                    <caption>Demandes déposées par région</caption>
                    <thead><tr><th>Région</th><th class="fs-num">Demandes</th></tr></thead>
                    <tbody>
                      <tr><td>Centre</td><td class="fs-num">38&nbsp;400</td></tr>
                      <tr><td>Hauts-Bassins</td><td class="fs-num">21&nbsp;700</td></tr>
                      <tr><td>Boucle du Mouhoun</td><td class="fs-num">12&nbsp;300</td></tr>
                      <tr><td>Est</td><td class="fs-num">8&nbsp;100</td></tr>
                      <tr><td>Sahel</td><td class="fs-num">5&nbsp;400</td></tr>
                    </tbody>
                  </table>
                </div>
              </details>
            </div>
          </div>
        </div>

        <div class="doc-demo fs-mt-6" data-libelle="Répartition">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-graphe">
              <p class="fs-graphe-titre">État des dossiers en cours</p>
              <p class="fs-graphe-note">12&nbsp;480 dossiers au 21 septembre 2026.</p>

              <div class="fs-repartition" role="img"
                   aria-label="En cours d'instruction 54 %, complément demandé 21 %, validés 18 %, rejetés 7 %">
                <span style="width:54%; background: var(--serie-2); color: var(--neutre-900);">54&nbsp;%</span>
                <span style="width:21%; background: var(--serie-4);">21&nbsp;%</span>
                <span style="width:18%; background: var(--serie-1);">18&nbsp;%</span>
                <span style="width:7%;  background: var(--serie-3);"></span>
              </div>

              <div class="fs-repartition-legende">
                <span style="--puce: var(--serie-2);">En cours d'instruction&nbsp;· 54&nbsp;%</span>
                <span style="--puce: var(--serie-4);">Complément demandé&nbsp;· 21&nbsp;%</span>
                <span style="--puce: var(--serie-1);">Validés&nbsp;· 18&nbsp;%</span>
                <span style="--puce: var(--serie-3);">Rejetés&nbsp;· 7&nbsp;%</span>
              </div>

              <details class="fs-graphe-donnees">
                <summary>Consulter les données sous forme de tableau</summary>
                <div class="fs-tableau-cadre">
                  <table class="fs-tableau fs-tableau--compact">
                    <caption>État des dossiers en cours</caption>
                    <thead>
                      <tr><th>État</th><th class="fs-num">Dossiers</th><th class="fs-num">Part</th></tr>
                    </thead>
                    <tbody>
                      <tr><td>En cours d'instruction</td><td class="fs-num">6&nbsp;739</td><td class="fs-num">54&nbsp;%</td></tr>
                      <tr><td>Complément demandé</td><td class="fs-num">2&nbsp;621</td><td class="fs-num">21&nbsp;%</td></tr>
                      <tr><td>Validés</td><td class="fs-num">2&nbsp;246</td><td class="fs-num">18&nbsp;%</td></tr>
                      <tr><td>Rejetés</td><td class="fs-num">874</td><td class="fs-num">7&nbsp;%</td></tr>
                    </tbody>
                  </table>
                </div>
              </details>
            </div>
          </div>
        </div>

        <div class="doc-demo fs-mt-6" data-libelle="Courbe">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-graphe">
              <p class="fs-graphe-titre">Délai moyen de traitement</p>
              <p class="fs-graphe-note">En jours ouvrés, de mars à septembre 2026. Données d'exemple.</p>

              <svg class="fs-courbe" viewBox="0 0 420 180" role="img"
                   aria-label="Le délai moyen passe de 9 jours ouvrés en mars à 3 jours ouvrés en septembre 2026, en baisse chaque mois.">
                <path class="fs-courbe-grille" d="M40 20h370M40 60h370M40 100h370"/>
                <path class="fs-courbe-axe" d="M40 140h370"/>
                <!-- Échelle linéaire : y = 140 - délai × (120 / 9), de 0 à 9 jours. -->
                <path class="fs-courbe-aire" d="M40 20 100 41.333 160 48 220 76 280 86.667 340 94.667 400 100V140H40Z"/>
                <path class="fs-courbe-trace" d="M40 20 100 41.333 160 48 220 76 280 86.667 340 94.667 400 100"/>
                <g class="fs-courbe-point">
                  <circle cx="40" cy="20" r="4"/><circle cx="100" cy="41.333" r="4"/>
                  <circle cx="160" cy="48" r="4"/><circle cx="220" cy="76" r="4"/>
                  <circle cx="280" cy="86.667" r="4"/><circle cx="340" cy="94.667" r="4"/>
                  <circle cx="400" cy="100" r="4"/>
                </g>
                <g class="fs-courbe-etiquette" text-anchor="middle">
                  <text x="40" y="168">Mars</text><text x="100" y="168">Avril</text>
                  <text x="160" y="168">Mai</text><text x="220" y="168">Juin</text>
                  <text x="280" y="168">Juil.</text><text x="340" y="168">Août</text>
                  <text x="400" y="168">Sept.</text>
                </g>
                <g class="fs-courbe-etiquette" text-anchor="end">
                  <text x="32" y="24">9 j</text><text x="32" y="64">6 j</text><text x="32" y="104">3 j</text>
                  <text x="32" y="144">0 j</text>
                </g>
              </svg>

              <details class="fs-graphe-donnees">
                <summary>Consulter les données sous forme de tableau</summary>
                <div class="fs-tableau-cadre">
                  <table class="fs-tableau fs-tableau--compact">
                    <caption>Délai moyen de traitement, en jours ouvrés</caption>
                    <thead><tr><th>Mois</th><th class="fs-num">Délai moyen</th></tr></thead>
                    <tbody>
                      <tr><td>Mars</td><td class="fs-num">9,0 j</td></tr>
                      <tr><td>Avril</td><td class="fs-num">7,4 j</td></tr>
                      <tr><td>Mai</td><td class="fs-num">6,9 j</td></tr>
                      <tr><td>Juin</td><td class="fs-num">4,8 j</td></tr>
                      <tr><td>Juillet</td><td class="fs-num">4,0 j</td></tr>
                      <tr><td>Août</td><td class="fs-num">3,4 j</td></tr>
                      <tr><td>Septembre</td><td class="fs-num">3,0 j</td></tr>
                    </tbody>
                  </table>
                </div>
              </details>
            </div>
          </div>
        </div>

        <h4 class="fs-mt-8">Palette de séries</h4>
        <p class="doc-bloc-note">
          Les séries sont ordonnées par <strong>écart de luminosité</strong> et non par
          teinte&nbsp;: un graphique doit rester lisible en niveaux de gris, à
          l'impression en noir et blanc comme pour un usager daltonien (guide, § 2.2).
          La palette ne compte ni jaune ni bleu&nbsp;: le guide proscrit d'associer le
          jaune au vert et le rouge au bleu. Elle alterne des verts, des rouges et des
          gris. Quand les séries portent un état, le vert et le rouge gardent leur
          sens conventionnel&nbsp;: le vert pour ce qui se passe bien, le rouge pour
          l'alerte ou le refus, un gris pour ce qui n'est ni l'un ni l'autre. La
          répartition ci-dessus applique cette règle.
        </p>
        <p class="doc-bloc-note">
          <strong>Cinq séries visibles au plus</strong>, ce qui respecte la limite de
          cinq couleurs par écran du guide (§ 2.2)&nbsp;; au-delà, un tableau vaut mieux
          qu'un graphique. La sixième teinte n'est qu'une couleur de remplacement,
          jamais une sixième série affichée en même temps que les cinq autres.
        </p>

        <div class="doc-roles">
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--serie-1)"></span>
            <div><span class="doc-role-nom">--serie-1</span><span class="doc-role-usage">Vert 600. Série principale, ou ce qui se passe bien</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--serie-2)"></span>
            <div><span class="doc-role-nom">--serie-2</span><span class="doc-role-usage">Neutre 400. Deuxième série, contraste de luminosité maximal avec la première</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--serie-3)"></span>
            <div><span class="doc-role-nom">--serie-3</span><span class="doc-role-usage">Rouge 700. Troisième série, ou ce qui est en alerte ou refusé</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--serie-4)"></span>
            <div><span class="doc-role-nom">--serie-4</span><span class="doc-role-usage">Neutre 700. Reste, autres, non renseigné</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--serie-5)"></span>
            <div><span class="doc-role-nom">--serie-5</span><span class="doc-role-usage">Vert 300. Cinquième série</span></div>
          </div>
          <div class="doc-role">
            <span class="doc-role-pastille" style="background: var(--serie-6)"></span>
            <div><span class="doc-role-nom">--serie-6</span><span class="doc-role-usage">Rouge 300. Remplacement, jamais en sixième série visible</span></div>
          </div>
        </div>

        <div class="doc-usages fs-mt-8">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Joindre systématiquement le tableau des données, replié sous le graphique.</li>
              <li>Donner au graphique un <code>role="img"</code> et un <code>aria-label</code> qui énonce la tendance, pas la liste des valeurs.</li>
              <li>Étiqueter directement chaque barre&nbsp;: une légende oblige l'œil à faire des allers-retours.</li>
              <li>Indiquer la source et la date d'arrêté des données.</li>
              <li>Faire partir l'axe des valeurs de zéro.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Le camembert&nbsp;: l'œil compare mal des angles. Une répartition en bande fait le même travail.</li>
              <li>Distinguer des séries par la seule couleur.</li>
              <li>Tronquer l'axe pour accentuer une variation.</li>
              <li>Les effets de profondeur, les dégradés et les animations d'apparition.</li>
              <li>Donner au vert ou au rouge un sens contraire à la convention&nbsp;: une série «&nbsp;rejetés&nbsp;» en vert, une série «&nbsp;validés&nbsp;» en rouge (guide, § 2.2).</li>
              <li>Ajouter du jaune ou du bleu à la palette&nbsp;: jaune et vert, rouge et bleu sont des associations que le guide écarte.</li>
              <li>Plus de cinq séries dans un même graphique.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Chronologie <code>.fs-chrono</code></h3>
        <div class="doc-demo" data-libelle="Chronologie">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <ol class="fs-chrono">
              <li data-etat="faite">
                <span class="fs-chrono-date">18 septembre 2026&nbsp;— 09&nbsp;h 12</span>
                <span class="fs-chrono-titre">Demande déposée</span>
                <p class="fs-legende">Dépôt effectué en ligne. Récépissé adressé par courrier électronique.</p>
              </li>
              <li data-etat="faite">
                <span class="fs-chrono-date">18 septembre 2026&nbsp;— 14&nbsp;h 40</span>
                <span class="fs-chrono-titre">Paiement encaissé</span>
                <p class="fs-legende">Droit de timbre de 500&nbsp;F&nbsp;CFA réglé par monnaie électronique.</p>
              </li>
              <li data-etat="courante">
                <span class="fs-chrono-date">19 septembre 2026</span>
                <span class="fs-chrono-titre">En cours d'instruction</span>
                <p class="fs-legende">Dossier transmis au service compétent de la mairie du lieu de naissance.</p>
              </li>
              <li>
                <span class="fs-chrono-date">À venir</span>
                <span class="fs-chrono-titre">Document disponible au retrait</span>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="retour-systeme">
      <h2>Retour système</h2>
      <p class="doc-section-intro">
        Une interface qui ne dit rien pendant qu'elle travaille est perçue comme
        défaillante. Sur réseau instable, le retour système n'est pas un confort&nbsp;:
        c'est ce qui évite les doubles soumissions et les abandons.
      </p>

      <div class="doc-bloc">
        <h3>Notification passagère <code>.fs-toast</code></h3>
        <p class="doc-bloc-note">
          Appelée par <code>Faso.notifier()</code>. Le conteneur est une région live
          polie&nbsp;: le message est annoncé sans interrompre la tâche en cours. Ce qui
          doit être retrouvé plus tard n'a pas sa place dans une notification.
        </p>
        <div class="doc-demo" data-libelle="Notifications">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--secondaire"
              onclick="Faso.notifier({titre:'Brouillon enregistré', texte:'Vous pourrez reprendre votre demande pendant trente jours.', ton:'succes'})">
              Notification de succès
            </button>
            <button type="button" class="fs-btn fs-btn--secondaire"
              onclick="Faso.notifier({titre:'Connexion interrompue', texte:'Vos modifications seront transmises au rétablissement du réseau.', ton:'alerte'})">
              Notification d'alerte
            </button>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Centre de notifications <code>.fs-notifications</code></h3>
        <p class="doc-bloc-note">
          Historique consultable, distinct de la notification passagère qui, elle,
          disparaît. Il ne porte <strong>jamais seul</strong> une information exigeant
          une action&nbsp;: un usager qui ne revient pas sur le service ne la verrait pas.
          Le SMS reste le canal qui l'atteint hors du service&nbsp;; le centre n'en garde
          que la trace.
        </p>

        <div class="doc-demo" data-libelle="Centre de notifications">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 560px;">
            <div class="fs-notifications">
              <div class="fs-notifications-entete">
                <h4 class="fs-notifications-titre">
                  Notifications
                  <span class="fs-compteur" data-notifications-compte>0</span>
                </h4>
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-tout-lu>
                  Tout marquer comme lu
                </button>
              </div>

              <ul class="fs-notifications-liste">
                <li class="fs-notification" data-lu="false">
                  <span class="fs-notification-puce" aria-hidden="true"></span>
                  <span class="fs-notification-icone fs-icone fs-icone--alerte" aria-hidden="true"></span>
                  <div class="fs-notification-corps">
                    <p class="fs-notification-titre">Pièce complémentaire attendue</p>
                    <p class="fs-notification-texte">Dossier BF-2026-004915&nbsp;: un justificatif de domicile est attendu avant le 30 septembre 2026.</p>
                    <time class="fs-notification-date" datetime="2026-09-19">19 septembre, 08&nbsp;h&nbsp;12</time>
                  </div>
                  <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-marquer-lu>Marquer comme lu</button>
                </li>

                <li class="fs-notification" data-lu="false">
                  <span class="fs-notification-puce" aria-hidden="true"></span>
                  <span class="fs-notification-icone fs-icone fs-icone--succes" aria-hidden="true"></span>
                  <div class="fs-notification-corps">
                    <p class="fs-notification-titre">Paiement encaissé</p>
                    <p class="fs-notification-texte">Droit de timbre de 500&nbsp;F&nbsp;CFA réglé pour le dossier BF-2026-004871.</p>
                    <time class="fs-notification-date" datetime="2026-09-18">18 septembre, 14&nbsp;h&nbsp;40</time>
                  </div>
                  <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-marquer-lu>Marquer comme lu</button>
                </li>

                <li class="fs-notification" data-lu="true">
                  <span class="fs-notification-puce" aria-hidden="true"></span>
                  <span class="fs-notification-icone fs-icone fs-icone--document" aria-hidden="true"></span>
                  <div class="fs-notification-corps">
                    <p class="fs-notification-titre">Demande enregistrée</p>
                    <p class="fs-notification-texte">Votre demande d'acte de naissance porte la référence BF-2026-004871.</p>
                    <time class="fs-notification-date" datetime="2026-09-18">18 septembre, 09&nbsp;h&nbsp;12</time>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Produire la liste côté serveur, pour qu'elle reste lisible sans JavaScript.</li>
              <li>Signaler le non-lu par trois repères (fond, graisse et pastille), dont aucun n'est la couleur seule.</li>
              <li>Dater chaque entrée avec un <code>&lt;time&gt;</code> exploitable.</li>
              <li>Rappeler la référence du dossier dans tout message de suivi.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Signaler une action à échéance uniquement dans le centre.</li>
              <li>Marquer comme lu au simple défilement&nbsp;: l'usager n'a rien confirmé.</li>
              <li>Accumuler sans limite&nbsp;: au-delà de trois mois, les entrées sont archivées.</li>
              <li>Notifier une étape purement technique sans effet pour l'usager.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Progression <code>.fs-jauge</code></h3>
        <p class="doc-bloc-note">
          Le guide fixe trois seuils (§ 7.2.1)&nbsp;: au-delà de 0,1&nbsp;s, la réponse ne
          paraît plus instantanée&nbsp;; au-delà de 1&nbsp;s, l'usager a le sentiment de
          perdre le contrôle&nbsp;; au-delà de 10&nbsp;s, il décroche. D'où trois réponses&nbsp;:
          rien sous la seconde, un indicateur d'attente au-delà, et une jauge
          d'avancement pour toute attente qui dépasse 6&nbsp;s (§ 7.2.2).
        </p>
        <div class="doc-demo" data-libelle="Progression">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div>
              <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
                <span class="fs-legende">Dossier complété</span>
                <span class="fs-legende fs-tabulaire">60&nbsp;%</span>
              </div>
              <div class="fs-jauge" data-valeur="60" aria-label="Dossier complété à 60 %">
                <div class="fs-jauge-valeur"></div>
              </div>
            </div>

            <div>
              <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
                <span class="fs-legende">Espace de stockage utilisé</span>
                <span class="fs-legende fs-tabulaire">92&nbsp;%</span>
              </div>
              <div class="fs-jauge fs-jauge--danger" data-valeur="92" aria-label="Espace utilisé à 92 %">
                <div class="fs-jauge-valeur"></div>
              </div>
            </div>

            <div class="fs-rangee">
              <span class="fs-sablier" role="status" aria-label="Chargement en cours"></span>
              <span class="fs-legende">Vérification du numéro CNIB auprès de l'ONI…</span>
            </div>
          </div>
        </div>

        <div class="doc-demo fs-mt-6" data-libelle="Traitement long">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div aria-busy="true">
              <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
                <span class="fs-legende">Envoi des pièces justificatives au service instructeur</span>
                <span class="fs-legende fs-tabulaire">40&nbsp;%</span>
              </div>
              <div class="fs-jauge" data-valeur="40" aria-label="Envoi des pièces justificatives, 40 %">
                <div class="fs-jauge-valeur"></div>
              </div>
              <div class="fs-rangee fs-rangee--entre fs-mt-4">
                <span class="fs-legende fs-tabulaire">2 fichiers sur 5, 14&nbsp;s écoulées</span>
                <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm">Annuler l'envoi</button>
              </div>
            </div>
          </div>
        </div>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Dire pourquoi l'on attend&nbsp;: «&nbsp;Envoi des pièces au service instructeur&nbsp;», pas «&nbsp;Veuillez patienter&nbsp;» (guide, § 7.2.2).</li>
              <li>Au-delà de 6&nbsp;s, afficher en texte le pourcentage atteint ou le temps écoulé, et faire avancer la jauge avec le traitement réel.</li>
              <li>Annoncer la fin par un message&nbsp;: une notification passagère, ou un encart de succès si le résultat doit rester visible.</li>
              <li>Faire interrompre réellement le traitement par «&nbsp;Annuler&nbsp;» ou «&nbsp;Arrêter&nbsp;», et rendre la main à l'usager.</li>
              <li>Poser <code>aria-busy="true"</code> sur la zone qui travaille&nbsp;: le pointeur prend la forme «&nbsp;en cours&nbsp;» et le reste de la page reste utilisable. Sur <code>&lt;body&gt;</code>, il prend la forme d'attente et signale que rien d'autre ne peut être lancé (guide, § 7.1.6).</li>
              <li>Placer la jauge dans la page quand l'usager peut continuer à travailler, dans une fenêtre modale quand il ne le peut pas.</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Une jauge qui avance selon une minuterie, sans rapport avec le traitement réel.</li>
              <li>Un simple indicateur d'attente pour un traitement qui dure plus de 6&nbsp;s.</li>
              <li>Un bouton «&nbsp;Annuler&nbsp;» qui masque la jauge en laissant le traitement se poursuivre.</li>
              <li>Laisser <code>aria-busy</code> en place une fois le traitement terminé.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Squelette de chargement <code>.fs-squelette</code></h3>
        <p class="doc-bloc-note">
          Préférable au sablier lorsque la forme du contenu attendu est connue&nbsp;:
          l'usager anticipe la mise en page et perçoit l'attente comme plus courte.
        </p>
        <div class="doc-demo" data-libelle="Squelette">
          <div class="doc-demo-scene doc-demo-scene--pile" style="max-width: 460px;">
            <div aria-hidden="true">
              <div class="fs-squelette fs-squelette--titre"></div>
              <div class="fs-squelette fs-squelette--texte"></div>
              <div class="fs-squelette fs-squelette--texte" style="width: 92%"></div>
              <div class="fs-squelette fs-squelette--texte" style="width: 74%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>État vide <code>.fs-vide</code></h3>
        <div class="doc-demo" data-libelle="État vide">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-vide">
              <h4 class="fs-vide-titre">Aucune demande en cours</h4>
              <p>Vos démarches déposées apparaîtront ici. Vous pourrez suivre leur instruction et télécharger les documents délivrés.</p>
              <a class="fs-btn fs-btn--principal" href="#">Rechercher une démarche</a>
            </div>
          </div>
        </div>
        <div class="doc-note fs-mt-4">
          Un état vide explique pourquoi la zone est vide et propose l'action qui la
          remplira. Un simple «&nbsp;Aucun résultat&nbsp;» laisse l'usager sans issue.
        </div>
      </div>
    </section>

    <!-- ===================================================== -->
    <section class="doc-section" id="superposition">
      <h2>Superposition</h2>
      <p class="doc-section-intro">
        Une superposition interrompt la tâche en cours&nbsp;: elle se justifie par une
        décision à prendre, jamais par un contenu qui aurait sa place dans la page.
      </p>

      <div class="doc-bloc">
        <h3>Accordéon <code>.fs-accordeon</code></h3>
        <p class="doc-bloc-note">
          Bâti sur <code>&lt;details&gt;</code> et <code>&lt;summary&gt;</code>&nbsp;: il
          fonctionne sans JavaScript et reste ouvrable par la recherche du
          navigateur. À l'impression, un panneau fermé ne sort pas&nbsp;: ce qui
          doit figurer sur papier ne se range pas dans un accordéon.
        </p>
        <div class="doc-demo" data-libelle="Accordéon">
          <div class="doc-demo-scene doc-demo-scene--pile">
            <div class="fs-accordeon">
              <details open>
                <summary>Puis-je déposer une demande pour un proche&nbsp;?</summary>
                <div class="fs-accordeon-corps">
                  <p>Oui, à condition de produire une procuration écrite et la copie de la pièce d'identité du mandant. Pour un mineur, le lien de filiation suffit.</p>
                </div>
              </details>
              <details>
                <summary>Que faire si je n'ai pas reçu mon récépissé&nbsp;?</summary>
                <div class="fs-accordeon-corps">
                  <p>Vérifiez le dossier de courrier indésirable de votre messagerie. Le récépissé reste téléchargeable depuis votre espace personnel pendant un an.</p>
                </div>
              </details>
              <details>
                <summary>Le paiement en ligne est-il obligatoire&nbsp;?</summary>
                <div class="fs-accordeon-corps">
                  <p>Non. Le droit de timbre peut être réglé au guichet lors du retrait, mais le délai de traitement ne commence qu'à réception du paiement.</p>
                </div>
              </details>
            </div>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Modale <code>.fs-modale</code></h3>
        <p class="doc-bloc-note">
          Fondée sur <code>&lt;dialog&gt;</code>, qui enferme nativement le focus et
          gère la touche Échap. À la fermeture, le script rend le focus à l'élément
          déclencheur. Sur écran étroit, la modale occupe tout l'écran.
        </p>
        <div class="doc-demo" data-libelle="Modale">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--danger" data-ouvre-modale="modale-suppression">
              Supprimer le brouillon
            </button>

            <dialog class="fs-modale" id="modale-suppression" aria-labelledby="modale-suppression-titre">
              <div class="fs-modale-entete">
                <h3 class="fs-modale-titre" id="modale-suppression-titre">Brouillon de demande</h3>
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-modale aria-label="Fermer"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
              </div>
              <div class="fs-modale-corps">
                <p>Souhaitez-vous vraiment supprimer le brouillon de la demande <code>BF-2026-004871</code>&nbsp;? Les trois pièces déjà téléversées seront supprimées avec lui.</p>
                <p>Cette action est irréversible.</p>
              </div>
              <div class="fs-modale-pied">
                <button type="button" class="fs-btn fs-btn--tertiaire" data-ferme-modale>Conserver</button>
                <button type="button" class="fs-btn fs-btn--danger" data-ferme-modale>Supprimer définitivement</button>
              </div>
            </dialog>
          </div>
        </div>
        <div class="doc-note fs-mt-4">
          Le libellé du bouton de confirmation décrit l'action («&nbsp;Supprimer
          définitivement&nbsp;»), et non «&nbsp;Oui&nbsp;». Un usager qui ne lit que les boutons
          doit comprendre ce qu'il déclenche. Le guide admet aussi «&nbsp;Continuer&nbsp;»
          et «&nbsp;Arrêter&nbsp;» dans une boîte de confirmation (ci-dessous)&nbsp;; pour une
          suppression, nommer l'action dit mieux ce qui sera perdu.
        </div>

        <h4 class="fs-mt-8">Boîtes de message</h4>
        <p class="doc-bloc-note">
          Quand le système doit informer, avertir, faire confirmer ou bloquer, il le
          fait dans une boîte de message construite sur la modale, toujours de la même
          façon (guide, § 7.1.1)&nbsp;: un titre qui dit d'où vient le message, une icône
          à gauche du texte qui en donne le type, un texte bref qui explique la cause
          et propose une suite, et les boutons propres à ce type. Si le message porte
          un identifiant, il se place en bas à droite, en petit&nbsp;: il sert au support,
          pas à l'usager.
        </p>

        <div class="fs-tableau-cadre">
          <table class="fs-tableau">
            <caption>Les quatre types de message (guide, § 7.1.2 à § 7.1.5)</caption>
            <thead>
              <tr><th scope="col">Type</th><th scope="col">Quand</th><th scope="col">Icône</th><th scope="col">Boutons</th><th scope="col">Bouton par défaut</th></tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">Information</th>
                <td>Une action s'est achevée, sans risque pour les données.</td>
                <td><code>fs-icone--information</code></td>
                <td>«&nbsp;OK&nbsp;»</td>
                <td>«&nbsp;OK&nbsp;». La boîte peut être recouverte&nbsp;: une notification passagère ou un encart suffit souvent.</td>
              </tr>
              <tr>
                <th scope="row">Avertissement</th>
                <td>L'action demandée peut s'avérer dangereuse&nbsp;; l'usager peut la poursuivre ou y renoncer. Le texte est affirmatif.</td>
                <td><code>fs-icone--alerte</code></td>
                <td>«&nbsp;Continuer&nbsp;», «&nbsp;Annuler&nbsp;», «&nbsp;Aide&nbsp;» le cas échéant</td>
                <td>«&nbsp;Annuler&nbsp;». Modale si les conséquences sont graves.</td>
              </tr>
              <tr>
                <th scope="row">Confirmation</th>
                <td>Obligatoire avant toute opération irréversible. C'est toujours une question.</td>
                <td><code>fs-icone--aide</code></td>
                <td>«&nbsp;Continuer&nbsp;», «&nbsp;Arrêter&nbsp;» ou «&nbsp;Annuler&nbsp;»</td>
                <td>«&nbsp;Continuer&nbsp;». Toujours modale&nbsp;: l'usager doit répondre pour poursuivre.</td>
              </tr>
              <tr>
                <th scope="row">Blocage</th>
                <td>L'action est impossible dans le contexte courant.</td>
                <td><code>fs-icone--erreur</code></td>
                <td>«&nbsp;OK&nbsp;»</td>
                <td>«&nbsp;OK&nbsp;», qui accuse réception et ferme le message.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="doc-demo fs-mt-6" data-libelle="Boîte de confirmation">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--principal" data-ouvre-modale="modale-confirmation">
              Transmettre la demande
            </button>

            <dialog class="fs-modale" id="modale-confirmation" aria-labelledby="modale-confirmation-titre" aria-describedby="modale-confirmation-texte">
              <div class="fs-modale-entete">
                <h3 class="fs-modale-titre" id="modale-confirmation-titre">Transmission de la demande</h3>
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-modale aria-label="Fermer"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
              </div>
              <div class="fs-modale-corps">
                <div class="fs-rangee fs-rangee--haut">
                  <span class="fs-icone fs-icone--aide fs-icone--lg" aria-hidden="true"></span>
                  <div id="modale-confirmation-texte" style="flex: 1; min-width: 0;">
                    <p>Souhaitez-vous vraiment transmettre la demande <code>BF-2026-004871</code> au service instructeur&nbsp;?</p>
                    <p>Une fois transmise, elle ne pourra plus être modifiée. Une pièce oubliée devra être envoyée en complément.</p>
                  </div>
                </div>
                <div class="fs-rangee fs-rangee--fin fs-mt-4">
                  <span class="fs-legende fs-faible">Message CNF-014</span>
                </div>
              </div>
              <div class="fs-modale-pied">
                <button type="button" class="fs-btn fs-btn--tertiaire" data-ferme-modale>Arrêter</button>
                <button type="button" class="fs-btn fs-btn--principal" data-ferme-modale autofocus>Continuer</button>
              </div>
            </dialog>
          </div>
        </div>
        <p class="fs-legende fs-mt-4">
          L'attribut <code>autofocus</code> fait de «&nbsp;Continuer&nbsp;» le bouton par
          défaut&nbsp;: <code>&lt;dialog&gt;</code> lui donne le focus à l'ouverture, et
          Entrée le déclenche. La touche Échap ferme la boîte sans rien transmettre,
          comme «&nbsp;Arrêter&nbsp;».
        </p>

        <div class="doc-usages fs-mt-6">
          <div class="doc-usage doc-usage--oui">
            <p class="doc-usage-titre">À faire</p>
            <ul>
              <li>Employer le vocabulaire de l'usager, dans un style concis et neutre (guide, § 7.1.1).</li>
              <li>Expliquer la cause, puis proposer ce que l'usager peut faire.</li>
              <li>Formuler une confirmation comme une vraie question&nbsp;: «&nbsp;Souhaitez-vous vraiment…&nbsp;» (guide, § 8.1.2).</li>
              <li>Réserver «&nbsp;Continuer&nbsp;» et «&nbsp;Arrêter&nbsp;» aux boîtes d'avertissement et de confirmation, où le guide les prévoit. Partout ailleurs, et notamment sur les boutons de formulaire et d'étape, le bouton nomme l'action&nbsp;: «&nbsp;Passer au paiement&nbsp;».</li>
            </ul>
          </div>
          <div class="doc-usage doc-usage--non">
            <p class="doc-usage-titre">À proscrire</p>
            <ul>
              <li>Les réponses «&nbsp;Oui&nbsp;» et «&nbsp;Non&nbsp;»&nbsp;: elles obligent à relire la question pour savoir ce que l'on déclenche (guide, § 7.1.1 et § 7.1.4).</li>
              <li>Les mots qui mettent l'usager en cause&nbsp;: «&nbsp;faute&nbsp;», «&nbsp;mauvaise manipulation&nbsp;», «&nbsp;illégal&nbsp;».</li>
              <li>Un code d'erreur à la place du texte, ou en tête du message.</li>
              <li>Une boîte de message pour une information que la page peut porter elle-même&nbsp;: un encart suffit.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Panneau latéral <code>.fs-panneau-lateral</code></h3>
        <div class="doc-demo" data-libelle="Panneau latéral">
          <div class="doc-demo-scene">
            <button type="button" class="fs-btn fs-btn--tertiaire" data-ouvre-panneau="panneau-filtres">
              Filtrer les résultats
            </button>

            <div class="fs-voile"></div>

            <aside class="fs-panneau-lateral" id="panneau-filtres" aria-hidden="true" aria-label="Filtres de recherche">
              <div class="fs-modale-entete">
                <h3 class="fs-modale-titre">Filtrer</h3>
                <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-panneau aria-label="Fermer les filtres"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
              </div>
              <div class="fs-modale-corps">
                <fieldset class="fs-fieldset" style="border:0; padding:0;">
                  <legend class="fs-label">État du dossier</legend>
                  <label class="fs-choix"><input type="checkbox" checked><span class="fs-choix-texte"><span>En cours d'instruction</span></span></label>
                  <label class="fs-choix"><input type="checkbox"><span class="fs-choix-texte"><span>Validé</span></span></label>
                  <label class="fs-choix"><input type="checkbox"><span class="fs-choix-texte"><span>Rejeté</span></span></label>
                </fieldset>
              </div>
              <div class="fs-modale-pied">
                <button type="button" class="fs-btn fs-btn--fantome" data-ferme-panneau>Réinitialiser</button>
                <button type="button" class="fs-btn fs-btn--principal" data-ferme-panneau>Appliquer</button>
              </div>
            </aside>
          </div>
        </div>
      </div>

      <div class="doc-bloc">
        <h3>Menu déroulant <code>.fs-menu</code></h3>
        <div class="doc-demo" data-libelle="Menu">
          <div class="doc-demo-scene">
            <div class="fs-menu" data-menu>
              <button type="button" class="fs-btn fs-btn--tertiaire" aria-haspopup="true" aria-expanded="false">
                Mon compte
              </button>
              <ul class="fs-menu-liste" hidden>
                <li><a href="#">Mes demandes</a></li>
                <li><a href="#">Mes documents</a></li>
                <li><a href="#">Coordonnées</a></li>
                <li><hr class="fs-menu-separateur"></li>
                <li><button type="button">Se déconnecter</button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection
