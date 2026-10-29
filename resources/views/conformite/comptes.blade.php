@extends('layouts.conformite', ['titre' => 'Comptes'])

@section('contenu')
<h1>Comptes de l'outil</h1>
@include('conformite._erreurs')

<h2 class="fs-mt-8">En attente de validation</h2>
@if ($enAttente->isEmpty())
  <p class="fs-attenue">Aucun compte en attente.</p>
@else
  <div class="fs-tableau-cadre">
    <table class="fs-tableau">
      <caption>Adresses confirmées hors .gov.bf, à valider</caption>
      <thead><tr><th>Nom</th><th>Adresse</th><th>Entité déclarée</th><th>Inscrit le</th><th><span class="fs-invisible">Décision</span></th></tr></thead>
      <tbody>
@foreach ($enAttente as $compte)
        <tr>
          <td>{{ $compte->name }}</td>
          <td>{{ $compte->email }}</td>
          <td>{{ $compte->entite }}</td>
          <td class="fs-nowrap">{{ $compte->created_at->format('d/m/Y') }}</td>
          <td>
            <div class="fs-actions">
              @include('conformite._decision', ['compte' => $compte, 'statut' => 'actif', 'role' => 'developpeur', 'libelle' => 'Valider', 'classe' => 'fs-btn--principal'])
              @include('conformite._decision', ['compte' => $compte, 'statut' => 'suspendu', 'role' => 'developpeur', 'libelle' => 'Refuser', 'classe' => 'fs-btn--tertiaire'])
            </div>
          </td>
        </tr>
@endforeach
      </tbody>
    </table>
  </div>
@endif

<h2 class="fs-mt-8">Tous les comptes</h2>
<div class="fs-tableau-cadre">
  <table class="fs-tableau">
    <caption>Comptes actifs et suspendus, par entité</caption>
    <thead><tr><th>Nom</th><th>Adresse</th><th>Entité</th><th>Rôle et statut</th></tr></thead>
    <tbody>
@foreach ($comptes as $compte)
      <tr>
        <td>{{ $compte->name }}</td>
        <td>{{ $compte->email }}</td>
        <td>{{ $compte->entite }}</td>
        <td>
          <form method="post" action="{{ route('conformite.comptes.modifier', $compte) }}" class="fs-rangee">
            @csrf
            <label class="fs-invisible" for="role-{{ $compte->id }}">Rôle de {{ $compte->name }}</label>
            <select class="fs-select" id="role-{{ $compte->id }}" name="role">
@foreach (['developpeur' => 'Développeur', 'referent' => 'Référent d\'entité', 'administrateur' => 'Administrateur'] as $valeur => $nom)
              <option value="{{ $valeur }}" @selected($compte->role === $valeur)>{{ $nom }}</option>
@endforeach
            </select>
            <label class="fs-invisible" for="statut-{{ $compte->id }}">Statut de {{ $compte->name }}</label>
            <select class="fs-select" id="statut-{{ $compte->id }}" name="statut">
              <option value="actif" @selected($compte->statut === 'actif')>Actif</option>
              <option value="suspendu" @selected($compte->statut === 'suspendu')>Suspendu</option>
            </select>
            <button type="submit" class="fs-btn fs-btn--secondaire fs-btn--sm">Enregistrer</button>
          </form>
        </td>
      </tr>
@endforeach
    </tbody>
  </table>
</div>

@endsection
