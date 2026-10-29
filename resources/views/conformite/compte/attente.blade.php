@extends('layouts.conformite', ['titre' => 'Compte en attente'])

@section('contenu')
<div class="fs-mesure">
  @if ($compte->statut === 'suspendu')
    <h1>Compte suspendu</h1>
    <p>Ce compte ne peut plus lancer d'analyse. Pour en connaître la raison, écrivez à l'équipe de la charte.</p>
  @else
    <h1>Compte en attente de validation</h1>
    <p>
      Votre adresse est confirmée. Comme elle n'est pas en <code>.gov.bf</code>, l'équipe
      de la charte valide le compte avant son ouverture&nbsp;; vous recevrez un message
      dès que ce sera fait.
    </p>
    <dl class="fs-definitions fs-mt-6">
      <div><dt>Nom</dt><dd>{{ $compte->name }}</dd></div>
      <div><dt>Adresse</dt><dd>{{ $compte->email }}</dd></div>
      <div><dt>Entité déclarée</dt><dd>{{ $compte->entite }}</dd></div>
    </dl>
  @endif
  @unless (config('charte.export'))
    <p class="fs-mt-6"><a href="{{ route('contact') }}">Écrire à l'équipe de la charte</a></p>
  @endunless
</div>
@endsection
