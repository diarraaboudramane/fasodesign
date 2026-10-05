@extends('layouts.base', ['titre' => $page['titre'], 'description' => $page['description']])

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu principal</a>

<div class="doc-barre">
  <a class="doc-menu-marque" href="{{ page('index') }}">
    <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="">
    <span><span class="doc-nom">Charte graphique</span><span class="doc-version">Burkina Faso&nbsp;— v2.0</span></span>
  </a>
  <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" data-bascule="doc-menu" aria-expanded="false" aria-controls="doc-menu">Sommaire</button>
</div>

<div class="doc-page">

  <nav class="doc-menu" id="doc-menu" aria-label="Navigation de la documentation">
    <a class="doc-menu-marque" href="{{ page('index') }}">
      <img src="{{ ressource('assets/img/armoiries.svg') }}" alt="">
      <span><span class="doc-nom">Charte graphique</span><span class="doc-version">Burkina Faso&nbsp;— v2.0</span></span>
    </a>

    <div class="doc-menu-groupe">
      <p class="doc-menu-titre">Le système</p>
      <ul>
@foreach (config('charte.pages') as $nom => $p)
        <li><a href="{{ page($nom) }}"@if ($nom === $courante) aria-current="page"@endif>{{ $p['libelle'] }}</a></li>
@endforeach
      </ul>
    </div>

    <div class="doc-menu-groupe">
      <p class="doc-menu-titre">{{ $page['sommaire'] }}</p>
      <div class="fs-sommaire" data-sommaire-auto></div>
    </div>
  </nav>

  <main class="doc-corps" id="contenu"@if ($page['teinte']) data-teinte="{{ $page['teinte'] }}"@endif>
@yield('contenu')
  </main>
</div>

@include('partials.scripts')
@endsection
