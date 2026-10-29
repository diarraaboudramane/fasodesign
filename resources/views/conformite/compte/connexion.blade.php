@extends('layouts.conformite', ['titre' => 'Se connecter'])

@section('contenu')
<div class="fs-mesure">
  <h1>Se connecter</h1>
  <p>Le contrôle de conformité est réservé aux comptes de l'outil. Pas encore de compte&nbsp;? <a href="{{ route('inscription') }}">Créer un compte</a>.</p>

  @include('conformite._erreurs')

  <form method="post" action="{{ route('connexion.ouvrir') }}" class="fs-pile fs-mt-6" novalidate>
    @csrf
    @include('conformite._champ', ['nom' => 'email', 'libelle' => 'Adresse électronique', 'type' => 'email', 'remplissage' => 'username'])
    @include('conformite._champ', ['nom' => 'password', 'libelle' => 'Mot de passe', 'type' => 'password', 'remplissage' => 'current-password'])

    <label class="fs-choix">
      <input type="checkbox" name="souvenir" value="1">
      <span class="fs-choix-texte">Rester connecté sur cet appareil</span>
    </label>

    <div class="fs-actions">
      <button type="submit" class="fs-btn fs-btn--principal">Se connecter</button>
      <a class="fs-btn fs-btn--tertiaire" href="{{ route('oubli') }}">Mot de passe oublié</a>
    </div>
  </form>
</div>
@endsection
