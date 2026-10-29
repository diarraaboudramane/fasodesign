@extends('layouts.conformite', ['titre' => 'Nouveau mot de passe'])

@section('contenu')
<div class="fs-mesure">
  <h1>Choisir un nouveau mot de passe</h1>

  @include('conformite._erreurs')

  <form method="post" action="{{ route('reinitialisation.enregistrer') }}" class="fs-pile fs-mt-6" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $jeton }}">
    @include('conformite._champ', ['nom' => 'email', 'libelle' => 'Adresse électronique', 'type' => 'email', 'valeur' => $email, 'remplissage' => 'username'])
    @include('conformite._champ', ['nom' => 'password', 'libelle' => 'Nouveau mot de passe', 'type' => 'password', 'aide' => '10 caractères au moins.', 'remplissage' => 'new-password'])
    @include('conformite._champ', ['nom' => 'password_confirmation', 'libelle' => 'Nouveau mot de passe, une seconde fois', 'type' => 'password', 'remplissage' => 'new-password'])
    <div class="fs-actions">
      <button type="submit" class="fs-btn fs-btn--principal">Enregistrer le mot de passe</button>
    </div>
  </form>
</div>
@endsection
