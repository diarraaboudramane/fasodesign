@extends('layouts.conformite', ['titre' => 'Mes analyses'])

@section('contenu')
<div class="fs-espace-grille">
  <div class="fs-espace-ariane">
    <nav class="fs-ariane" aria-label="Fil d'Ariane">
      <ol>
        <li><a href="{{ route('conformite') }}">Contrôle de conformité</a></li>
        <li><span aria-current="page">Mes analyses</span></li>
      </ol>
    </nav>
  </div>

  @include('conformite._menu')

  <div class="fs-espace-contenu fs-pile fs-pile--large">
    <div>
      <h1>Nouvelle analyse</h1>
      <p class="fs-attenue">Il vous reste {{ $restantes }} analyse(s) pour aujourd'hui.</p>
    </div>

    @include('conformite._erreurs')

    <form method="post" action="{{ route('conformite.lancer') }}" enctype="multipart/form-data" class="fs-pile" novalidate data-lancement>
      @csrf

      <fieldset class="fs-groupe" id="mode">
        <legend class="fs-label">Que voulez-vous analyser&nbsp;? <span class="fs-requis" aria-hidden="true">*</span></legend>
        <label class="fs-choix">
          <input type="radio" name="mode" value="adresse" @checked(old('mode', 'adresse') === 'adresse')>
          <span class="fs-choix-texte">Un site en ligne, par son adresse</span>
        </label>
        <label class="fs-choix">
          <input type="radio" name="mode" value="archive" @checked(old('mode') === 'archive')>
          <span class="fs-choix-texte">Le code source, en archive ZIP</span>
        </label>
      </fieldset>

      {{-- Les champs de chaque mode : le script de la page n'affiche que ceux du
           mode choisi. Sans script, tout reste visible et le serveur trie. --}}
      <div class="fs-pile" data-mode-bloc="adresse">
      @include('conformite._champ', [
          'nom' => 'adresse', 'libelle' => 'Adresse du site', 'type' => 'url', 'requis' => false,
          'aide' => 'Pour une analyse par adresse. Exemple : https://eservices.gov.bf',
      ])

      <div class="fs-champ" id="rendu" @error('rendu') data-etat="erreur" @enderror>
        <label class="fs-choix">
          <input type="checkbox" name="rendu" value="1" @checked(old('rendu')) @disabled(! $renduDisponible) aria-describedby="rendu-aide">
          <span class="fs-choix-texte">Exécuter le JavaScript du site</span>
        </label>
        <p class="fs-aide" id="rendu-aide">
@if ($renduDisponible)
          Pour une application React, Angular ou Vue rendue dans le navigateur. L'analyse est plus lente, une à deux minutes, et mesure aussi les contrastes affichés et le poids réel des pages.
@else
          Le rendu JavaScript n'est pas installé sur ce serveur : l'analyse lit le HTML sans exécuter les scripts.
@endif
        </p>
        @error('rendu')
          <p class="fs-message fs-message--erreur">{{ $message }}</p>
        @enderror
      </div>
      </div>

      <div class="fs-champ" data-mode-bloc="archive" @error('archive') data-etat="erreur" @enderror>
        <label class="fs-label" for="archive">Archive ZIP du projet</label>
        <p class="fs-aide" id="archive-aide">Pour une analyse par archive. {{ $archiveMaxMo }} Mo au plus ; les dossiers node_modules, vendor et dist sont ignorés, inutile de les inclure.</p>
        <input class="fs-saisie" type="file" id="archive" name="archive" accept=".zip,application/zip"
               aria-describedby="archive-aide" @error('archive') aria-invalid="true" @enderror>
        @error('archive')
          <p class="fs-message fs-message--erreur">{{ $message }}</p>
        @enderror
      </div>

      <div class="fs-actions">
        <button type="submit" class="fs-btn fs-btn--principal" data-lancer>Lancer l'analyse</button>
      </div>

      {{-- Progression : l'envoi de l'archive avec sa jauge, puis l'analyse
           avec le sablier. Affichée par le script pendant le lancement. --}}
      <div class="fs-encart fs-encart--info" data-progression hidden>
        <span class="fs-encart-marque fs-icone fs-icone--horloge" aria-hidden="true"></span>
        <div class="fs-encart-corps" style="flex: 1; min-width: 0;">
          <p class="fs-encart-titre" data-progression-titre role="status">Envoi de l'archive</p>
          <div data-progression-envoi>
            <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
              <span class="fs-legende" data-progression-texte></span>
              <span class="fs-legende fs-tabulaire" data-progression-pourcent>0&nbsp;%</span>
            </div>
            <div class="fs-jauge" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="Envoi de l'archive">
              <div class="fs-jauge-valeur" style="width: 0%;"></div>
            </div>
          </div>
          <div class="fs-rangee fs-mt-4" data-progression-analyse hidden>
            <span class="fs-sablier" aria-hidden="true"></span>
            <span data-progression-analyse-texte>Analyse en cours&nbsp;: l'outil lit les fichiers et applique les règles de la charte.</span>
          </div>
          <div class="fs-actions fs-mt-4">
            <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" data-progression-annuler>Annuler l'envoi</button>
          </div>
        </div>
      </div>
    </form>

    <div id="precedentes">
      <h2>Analyses précédentes</h2>
@if ($analyses->isEmpty())
      <p class="fs-attenue">Aucune analyse pour l'instant.</p>
@else
      <div class="fs-tableau-cadre">
        <table class="fs-tableau">
          <caption>Vos analyses, la plus récente en premier</caption>
          <thead><tr><th>Date</th><th>Cible</th><th>Niveau</th><th class="fs-num">Bloquantes</th><th class="fs-num">Majeures</th><th class="fs-num">Mineures</th></tr></thead>
          <tbody>
@foreach ($analyses as $analyse)
            <tr>
              <td class="fs-nowrap">{{ $analyse->created_at->format('d/m/Y H:i') }}</td>
              <td><a href="{{ route('conformite.bilan', $analyse) }}">{{ $analyse->cible }}</a></td>
              <td>@include('conformite._niveau', ['analyse' => $analyse])</td>
              <td class="fs-num">{{ $analyse->etat === 'terminee' ? $analyse->bloquants : '' }}</td>
              <td class="fs-num">{{ $analyse->etat === 'terminee' ? $analyse->majeurs : '' }}</td>
              <td class="fs-num">{{ $analyse->etat === 'terminee' ? $analyse->mineurs : '' }}</td>
            </tr>
@endforeach
          </tbody>
        </table>
      </div>
      {{ $analyses->links('conformite._pagination') }}
@endif
    </div>
  </div>

  <aside class="fs-espace-actions" aria-label="Aide">
    <div class="fs-carte">
      <div class="fs-carte-corps">
        <h2 class="fs-carte-titre">Avant de lancer</h2>
        <ul class="fs-liste-puces">
          <li>Une analyse par adresse dure de quelques secondes à une minute ou deux.</li>
          <li>Le bilan reste consultable un an, puis il est supprimé.</li>
          <li>Un écart vous paraît faux&nbsp;? Signalez-le depuis le bilan.</li>
        </ul>
        <p><a href="{{ markdown() }}">Toute la charte en Markdown</a>, à donner avec le bilan à votre assistant de code.</p>
      </div>
    </div>
  </aside>
</div>
<script src="{{ ressource('assets/js/conformite.js') }}" defer></script>
@endsection
