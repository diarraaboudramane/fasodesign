@extends('layouts.conformite', ['titre' => 'Mot de passe oublié'])

@section('contenu')
<div class="fs-mesure">
  <h1>Mot de passe oublié</h1>
  <p>Indiquez l'adresse de votre compte&nbsp;: vous y recevrez un lien pour choisir un nouveau mot de passe.</p>

  @include('conformite._erreurs')

  <form method="post" action="{{ route('oubli.envoyer') }}" class="fs-pile fs-mt-6" novalidate>
    @csrf
    @include('conformite._champ', ['nom' => 'email', 'libelle' => 'Adresse électronique', 'type' => 'email', 'remplissage' => 'email'])
    <div class="fs-actions">
      <button type="submit" class="fs-btn fs-btn--principal">Recevoir le lien</button>
      <a class="fs-btn fs-btn--tertiaire" href="{{ route('connexion') }}">Revenir à la connexion</a>
    </div>
  </form>
</div>
@endsection
