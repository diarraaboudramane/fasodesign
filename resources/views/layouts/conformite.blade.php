@extends('layouts.base', [
    'titre' => ($titre ?? 'Contrôle de conformité').'&nbsp;— Charte graphique de l\'administration burkinabè',
    'description' => "Vérifier qu'un site ou un projet de l'administration respecte la charte graphique, et en obtenir le bilan.",
    'espace' => auth()->check() ? 'fs-espace-prive' : 'fs-espace-public',
])

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu principal</a>

<div class="fs-officiel">
  <div class="fs-conteneur">
    <span class="fs-officiel-sceau">Site officiel de l'administration burkinabè</span>
    <span class="fs-masque-mobile">Domaine vérifié <code>gov.bf</code></span>
  </div>
</div>

<div class="fs-filet"></div>

<header class="fs-entete">
  <div class="fs-conteneur">
    <div class="fs-entete-haut">
      <a class="fs-marque" href="{{ auth()->check() ? route('conformite.espace') : page('index') }}">
        <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="Armoiries du Burkina Faso">
        <span class="fs-marque-texte">
          <span class="fs-marque-pays">Burkina Faso</span>
          <span class="fs-marque-devise">La Patrie ou la Mort, nous Vaincrons</span>
          <span class="fs-marque-entite">Contrôle de conformité à la charte graphique</span>
        </span>
      </a>

      <div class="fs-entete-outils">
@auth
        <span class="fs-masque-mobile fs-legende">{{ auth()->user()->name }}</span>
        <form method="post" action="{{ route('deconnexion') }}">
          @csrf
          <button type="submit" class="fs-btn fs-btn--secondaire fs-btn--sm">Se déconnecter</button>
        </form>
@else
        <a class="fs-btn fs-btn--secondaire fs-btn--sm" href="{{ route('connexion') }}">Se connecter</a>
@endauth
      </div>
    </div>
  </div>

  <nav class="fs-nav" id="nav-principale" aria-label="Navigation principale">
    <div class="fs-conteneur">
      <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm fs-nav-bascule"
              data-bascule="nav-principale" aria-expanded="false" aria-controls="nav-principale"
              style="margin-block: var(--espace-3);">
        Menu
      </button>
      <ul class="fs-nav-liste">
        <li><a class="fs-nav-lien" href="{{ page('index') }}">La charte</a></li>
@auth
        <li><a class="fs-nav-lien" href="{{ route('conformite.espace') }}"@if (request()->routeIs('conformite.espace', 'conformite.bilan')) aria-current="page"@endif>Mes analyses</a></li>
@if (auth()->user()->estAdministrateur())
        <li><a class="fs-nav-lien" href="{{ route('conformite.comptes') }}"@if (request()->routeIs('conformite.comptes')) aria-current="page"@endif>Comptes</a></li>
        <li><a class="fs-nav-lien" href="{{ route('conformite.signalements') }}"@if (request()->routeIs('conformite.signalements')) aria-current="page"@endif>Écarts signalés</a></li>
@endif
@else
        <li><a class="fs-nav-lien" href="{{ route('conformite') }}"@if (request()->routeIs('conformite')) aria-current="page"@endif>Contrôle de conformité</a></li>
@endauth
      </ul>
    </div>
  </nav>
</header>

<main id="contenu" class="fs-conteneur doc-page-simple">
@if (session('statut'))
  <div class="fs-encart fs-encart--succes" role="status" style="margin-bottom: var(--espace-6);">
    <span class="fs-encart-marque fs-icone fs-icone--succes" aria-hidden="true"></span>
    <div class="fs-encart-corps"><p>{{ session('statut') }}</p></div>
  </div>
@endif
@yield('contenu')
</main>

<footer class="fs-pied">
  <div class="fs-conteneur">
    <div class="fs-pied-bas">
      <p>Le bilan constate le respect des règles vérifiables automatiquement, à une date donnée, sur ce qui a été soumis. Il ne vaut ni audit d'accessibilité ni homologation.</p>
      <p><a href="{{ page('index') }}">Charte graphique, version {{ \App\Support\Documentation::version() }}</a></p>
    </div>
  </div>
</footer>

<script src="{{ ressource('assets/js/faso.js') }}" defer></script>
@endsection
