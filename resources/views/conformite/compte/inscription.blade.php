@extends('layouts.conformite', ['titre' => 'Créer un compte'])

@section('contenu')
<div class="fs-mesure">
  <h1>Créer un compte</h1>
  <p>
    Un compte avec une adresse en <code>.gov.bf</code> est ouvert dès que l'adresse
    est confirmée. Un prestataire ou une autre adresse attend la validation de
    l'équipe de la charte&nbsp;: indiquez alors l'administration pour laquelle vous
    travaillez.
  </p>

  @include('conformite._erreurs')

  <form method="post" action="{{ route('inscription.enregistrer') }}" class="fs-pile fs-mt-6" novalidate>
    @csrf
    @include('conformite._champ', ['nom' => 'name', 'libelle' => 'Nom et prénom', 'remplissage' => 'name'])
    @include('conformite._champ', ['nom' => 'email', 'libelle' => 'Adresse électronique', 'type' => 'email', 'remplissage' => 'email'])
    @include('conformite._champ', ['nom' => 'entite', 'libelle' => 'Ministère, institution ou société', 'aide' => 'Pour un prestataire : la société, puis l\'administration mandante. Exemple : « Société X pour le ministère de la Santé ».', 'remplissage' => 'organization'])
    @include('conformite._champ', ['nom' => 'password', 'libelle' => 'Mot de passe', 'type' => 'password', 'aide' => '10 caractères au moins.', 'remplissage' => 'new-password'])
    @include('conformite._champ', ['nom' => 'password_confirmation', 'libelle' => 'Mot de passe, une seconde fois', 'type' => 'password', 'remplissage' => 'new-password'])

    <div class="fs-actions">
      <button type="submit" class="fs-btn fs-btn--principal">Créer le compte</button>
      <a class="fs-btn fs-btn--tertiaire" href="{{ route('connexion') }}">J'ai déjà un compte</a>
    </div>
  </form>
</div>
@endsection
