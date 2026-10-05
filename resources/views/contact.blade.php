@extends('layouts.base', [
    'titre' => "Nous écrire&nbsp;— Charte graphique de l'administration burkinabè",
    'description' => "Écrire à l'équipe de la charte graphique des services numériques de l'administration burkinabè.",
])

@push('tete')
<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer></script>
@endpush

@section('corps')
<a class="fs-evitement" href="#contenu">Aller au contenu principal</a>

<div class="fs-filet"></div>

<main id="contenu" class="fs-conteneur fs-mesure doc-page-simple">

  <p><a href="{{ page('index') }}">Charte graphique de l'administration burkinabè</a></p>

  <h1>Nous écrire</h1>
  <p>
    Une question sur la charte, un composant qui manque, une erreur dans la
    documentation&nbsp;: l'équipe vous répond à l'adresse que vous indiquez.
  </p>

  @if (session('envoye'))
    <div class="fs-encart fs-encart--succes" role="status">
      <span class="fs-encart-marque fs-icone fs-icone--succes" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">Message transmis</p>
        <p>Merci. Votre message a bien été transmis à l'équipe de la charte.</p>
      </div>
    </div>
  @endif

  @if ($errors->any())
    <div class="fs-recap-erreurs" role="alert" tabindex="-1">
      <h2>{{ $errors->count() === 1 ? 'Une information doit être corrigée' : $errors->count().' informations doivent être corrigées' }}</h2>
      <ul>
        @foreach ($errors->messages() as $champ => $messages)
          <li><a href="#{{ $champ === 'g-recaptcha-response' ? 'verification' : $champ }}">{{ $messages[0] }}</a></li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('contact.envoyer') }}" class="fs-pile fs-mt-8" novalidate>
    @csrf

    @foreach ([
        'nom' => ['Votre nom', 'text', 'name'],
        'courriel' => ['Votre adresse électronique', 'email', 'email'],
        'objet' => ['Objet', 'text', 'off'],
    ] as $champ => [$libelle, $type, $remplissage])
      <div class="fs-champ" @error($champ) data-etat="erreur" @enderror>
        <label class="fs-label" for="{{ $champ }}">
          {{ $libelle }} <span class="fs-requis" aria-hidden="true">*</span>
        </label>
        <input class="fs-saisie" type="{{ $type }}" id="{{ $champ }}" name="{{ $champ }}"
               value="{{ old($champ) }}" autocomplete="{{ $remplissage }}" required
               @error($champ) aria-invalid="true" aria-describedby="{{ $champ }}-erreur" @enderror>
        @error($champ)
          <p class="fs-message fs-message--erreur" id="{{ $champ }}-erreur">{{ $message }}</p>
        @enderror
      </div>
    @endforeach

    <div class="fs-champ" @error('message') data-etat="erreur" @enderror>
      <label class="fs-label" for="message">
        Votre message <span class="fs-requis" aria-hidden="true">*</span>
      </label>
      <textarea class="fs-zone" id="message" name="message" maxlength="4000" required
                @error('message') aria-invalid="true" aria-describedby="message-erreur" @enderror>{{ old('message') }}</textarea>
      @error('message')
        <p class="fs-message fs-message--erreur" id="message-erreur">{{ $message }}</p>
      @enderror
    </div>

    <div class="fs-champ" id="verification" @error('g-recaptcha-response') data-etat="erreur" @enderror>
      <p class="fs-label">Vérification anti-robot</p>
      <div class="g-recaptcha" data-sitekey="{{ $cleSite }}"></div>
      <noscript>
        <p class="fs-message fs-message--erreur">
          Cette vérification demande JavaScript. Activez-le pour envoyer le formulaire.
        </p>
      </noscript>
      @error('g-recaptcha-response')
        <p class="fs-message fs-message--erreur">{{ $message }}</p>
      @enderror
      @error('envoi')
        <p class="fs-message fs-message--erreur">{{ $message }}</p>
      @enderror
      <p class="fs-aide">
        Cette page est protégée par reCAPTCHA&nbsp;: Google reçoit des informations
        sur votre navigation pour cette vérification. Les
        <a href="https://policies.google.com/privacy">règles de confidentialité</a> et les
        <a href="https://policies.google.com/terms">conditions d'utilisation</a> de Google s'appliquent.
      </p>
    </div>

    <div class="fs-actions">
      <button type="submit" class="fs-btn fs-btn--principal">Envoyer le message</button>
    </div>
  </form>

</main>

<script src="{{ ressource('assets/js/faso.js') }}" defer></script>
@endsection
