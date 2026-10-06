@extends('layouts.base', [
    'titre' => "Vérification&nbsp;— Charte graphique de l'administration burkinabè",
    'description' => "Vérification anti-robot avant d'accéder à la charte graphique des services numériques de l'administration burkinabè.",
])

@push('tete')
<meta name="robots" content="noindex, follow">
<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer></script>
@endpush

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu principal</a>

<div class="fs-filet"></div>

<main id="contenu" class="fs-conteneur fs-mesure doc-page-simple">

  <span class="fs-surtitre">Charte graphique de l'administration burkinabè</span>
  <h1>Une vérification avant d'entrer</h1>
  <p class="fs-chapeau">
    Cochez la case ci-dessous pour accéder à la charte. Elle protège le site
    contre les robots qui en aspirent le contenu. Une fois cochée, elle ne vous
    est redemandée qu'après {!! \App\Support\Duree::lisible(config('charte.verification.duree')) !!}
    sans aucune page consultée sur cet appareil.
  </p>

  @if ($errors->any())
    <div class="fs-recap-erreurs" role="alert" tabindex="-1">
      <h2>La vérification n'a pas abouti</h2>
      <ul>
        <li><a href="#verification">{{ $errors->first() }}</a></li>
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('verification.valider') }}" class="fs-pile fs-mt-8">
    @csrf
    <input type="hidden" name="retour" value="{{ $retour }}">

    <div class="fs-champ" id="verification" @error('g-recaptcha-response') data-etat="erreur" @enderror>
      <p class="fs-label">Vérification anti-robot</p>
      <div class="g-recaptcha" data-sitekey="{{ $cleSite }}"></div>
      <noscript>
        <p class="fs-message fs-message--erreur">
          Cette vérification demande JavaScript. Activez-le, puis rechargez la page.
        </p>
      </noscript>
      @error('g-recaptcha-response')
        <p class="fs-message fs-message--erreur">{{ $message }}</p>
      @enderror
      <p class="fs-aide">
        Si une série d'images s'affiche, l'icône de casque propose une épreuve
        sonore à la place. La case est fournie par Google reCAPTCHA&nbsp;: Google
        reçoit des informations sur votre navigation pour cette vérification. Les
        <a href="https://policies.google.com/privacy">règles de confidentialité</a> et les
        <a href="https://policies.google.com/terms">conditions d'utilisation</a> de Google s'appliquent.
      </p>
    </div>

    <div class="fs-actions">
      <button type="submit" class="fs-btn fs-btn--principal">Accéder à la charte</button>
    </div>
  </form>

</main>
@endsection
