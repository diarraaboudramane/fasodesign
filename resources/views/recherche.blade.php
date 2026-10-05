@extends('layouts.base', [
    'titre' => ($requete === '' ? 'Rechercher' : 'Recherche&nbsp;: '.e($requete))
        ."&nbsp;— Charte graphique de l'administration burkinabè",
    'description' => 'Rechercher dans toute la documentation de la charte graphique des services numériques de l\'administration burkinabè.',
])

@push('tete')
{{-- Une page de résultats n'a rien à faire dans un moteur de recherche. --}}
<meta name="robots" content="noindex, follow">
@endpush

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu principal</a>

<div class="fs-filet"></div>

<main id="contenu" class="fs-conteneur fs-mesure doc-page-simple">

  <p><a href="{{ page('index') }}">Charte graphique de l'administration burkinabè</a></p>

  <h1>Rechercher dans la charte</h1>

  @include('partials.recherche', ['id' => 'recherche', 'valeur' => $requete])

  @if ($requete !== '')
    <p class="fs-mt-6" role="status">
      @if (count($resultats) === 0)
        Aucun résultat pour « {{ $requete }} ».
      @elseif (count($resultats) === 1)
        Un résultat pour « {{ $requete }} ».
      @else
        {{ count($resultats) }}{{ count($resultats) === \App\Support\Recherche::MAX_RESULTATS ? ' premiers' : '' }}
        résultats pour « {{ $requete }} ».
      @endif
    </p>

    @if (count($resultats) === 0)
      <div class="fs-encart fs-encart--info">
        <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
        <div class="fs-encart-corps">
          <p class="fs-encart-titre">Quelques pistes</p>
          <p>
            Vérifiez l'orthographe, essayez un mot plus général ou moins de mots&nbsp;:
            tous les mots saisis doivent figurer dans la même section. Le
            <a href="{{ page('composants') }}">catalogue des composants</a> et les
            <a href="{{ page('fondations') }}">fondations</a> se parcourent aussi par leur sommaire.
          </p>
        </div>
      </div>
    @else
      <ol class="fs-pile fs-mt-6">
        @foreach ($resultats as $resultat)
          <li>
            <p class="fs-surtitre">{{ $resultat['libelle'] }}</p>
            <p class="fs-mt-0"><a href="{{ $resultat['url'] }}"><strong>{!! $resultat['titre'] !!}</strong></a></p>
            <p class="fs-legende fs-mt-0">{!! $resultat['extrait'] !!}</p>
          </li>
        @endforeach
      </ol>
    @endif
  @endif

</main>

<script src="{{ ressource('assets/js/faso.js') }}" defer></script>
@endsection
