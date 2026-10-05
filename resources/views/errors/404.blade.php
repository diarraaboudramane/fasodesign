@extends('layouts.base', [
    'titre' => "Page introuvable — Charte graphique de l'administration burkinabè",
    'description' => 'Cette adresse ne correspond à aucune page de la documentation de la charte graphique.',
])

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu</a>

<div class="fs-filet"></div>

<main id="contenu" class="fs-conteneur" style="padding-block: var(--espace-20);">

  <div class="fs-vide" style="border-style: solid;">
    <span class="fs-badge fs-badge--neutre">Erreur 404</span>
    <h1 class="fs-vide-titre">Cette page n'existe pas ou a été déplacée</h1>
    <p class="fs-mesure">
      L'adresse saisie est peut-être incomplète, ou la documentation a été
      réorganisée. Les huit pages du système sont accessibles depuis l'accueil.
    </p>
    <div class="fs-actions">
      <a class="fs-btn fs-btn--tertiaire" href="{{ page('index') }}">Retour à l'accueil</a>
      <a class="fs-btn fs-btn--principal" href="{{ page('composants') }}">Voir les composants</a>
    </div>
  </div>

  <p class="fs-legende fs-mt-8 fs-mesure">
    Si vous suiviez un lien depuis un autre site, il désignait sans doute une
    version antérieure de la charte. Les versions retirées et leur sort sont
    décrits dans la page <a href="{{ page('gouvernance') }}">Gouvernance</a>.
  </p>

</main>

<script src="{{ ressource('assets/js/faso.js') }}" defer></script>
@endsection
