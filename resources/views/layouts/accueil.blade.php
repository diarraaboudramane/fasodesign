@extends('layouts.base', ['titre' => $page['titre'], 'description' => $page['description']])

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu principal</a>

<!-- Bandeau d'identification officielle : premier élément de la page,
     avant même la marque. L'usager doit pouvoir vérifier qu'il est sur
     un service authentique avant de lire quoi que ce soit d'autre. -->
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
      <a class="fs-marque" href="{{ page('index') }}">
        <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="Armoiries du Burkina Faso">
        <span class="fs-marque-texte">
          <span class="fs-marque-pays">Burkina Faso</span>
          <span class="fs-marque-devise">La Patrie ou la Mort, nous Vaincrons</span>
          <span class="fs-marque-entite">Charte graphique des services numériques</span>
        </span>
      </a>

      <div class="fs-entete-outils">
        <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-bascule-theme aria-pressed="false">
          <span data-theme-libelle>Thème sombre</span>
        </button>
        <a class="fs-btn fs-btn--secondaire fs-btn--sm fs-masque-mobile" href="{{ page('fondations') }}">Consulter la charte</a>
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
@foreach (config('charte.pages') as $nom => $p)
        <li><a class="fs-nav-lien" href="{{ page($nom) }}"@if ($nom === $courante) aria-current="page"@endif>{{ $p['libelle'] }}</a></li>
@endforeach
      </ul>
    </div>
  </nav>
</header>

<main id="contenu">
@yield('contenu')
</main>

<footer class="fs-pied">
  <div class="fs-tissage" style="--tissage-fond: var(--surface-enfoncee);"></div>
  <div class="fs-conteneur">
    <div class="fs-pied-corps">
      <div>
        <a class="fs-marque" href="{{ page('index') }}" style="padding: 0;">
          <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="">
          <span class="fs-marque-texte">
            <span class="fs-marque-pays">Burkina Faso</span>
            <span class="fs-marque-devise">La Patrie ou la Mort, nous Vaincrons</span>
          </span>
        </a>
        <p class="fs-legende fs-mt-4" style="max-width: 36ch;">
          Charte graphique des services numériques de l'administration burkinabè.
          Document de référence à destination des équipes de conception et de
          développement des ministères et établissements publics.
        </p>
      </div>

      <div>
        <p class="fs-pied-titre">Le système</p>
        <ul>
@foreach (config('charte.pages') as $nom => $p)
@continue ($nom === 'index')
          <li><a href="{{ page($nom) }}">{{ $p['libelle'] }}</a></li>
@endforeach
        </ul>
      </div>

      <div>
        <p class="fs-pied-titre">Ressources</p>
        <ul>
          <li><a href="#principes">Principes directeurs</a></li>
          <li><a href="{{ page('fondations') }}#marque">Usage de l'emblème</a></li>
          <li><a href="{{ page('accessibilite') }}#redaction">Ton administratif</a></li>
          <li><a href="{{ page('composants-metier') }}#confiance">Confiance numérique</a></li>
        </ul>
      </div>

      <div>
        <p class="fs-pied-titre">Obligations</p>
        <ul>
          <li><a href="#">Mentions légales</a></li>
          <li><a href="#">Politique de confidentialité</a></li>
          <li><a href="#">Déclaration d'accessibilité</a></li>
          <li><a href="#">Plan du site</a></li>
@unless (config('charte.export'))
{{-- Le site exporté est statique : il ne peut pas recevoir de formulaire. --}}
          <li><a href="{{ route('contact') }}">Nous écrire</a></li>
@endunless
        </ul>
      </div>
    </div>

    <div class="fs-pied-bas">
      <p>Version 2.0&nbsp;— Septembre 2026</p>
      <p>Sauf mention contraire, les contenus de cette charte sont réutilisables par les administrations publiques.</p>
    </div>
  </div>
</footer>

@include('partials.scripts')
@endsection
