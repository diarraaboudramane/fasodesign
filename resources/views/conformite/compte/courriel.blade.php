@extends('layouts.conformite', ['titre' => 'Confirmer votre adresse'])

@section('contenu')
<div class="fs-mesure">
  <h1>Confirmer votre adresse</h1>
  <p>
    Un lien de confirmation a été envoyé à <strong>{{ auth()->user()->email }}</strong>.
    Ouvrez-le pour activer votre compte. Il est valable une heure.
  </p>
  <form method="post" action="{{ route('conformite.courriel.renvoyer') }}" class="fs-mt-6">
    @csrf
    <button type="submit" class="fs-btn fs-btn--secondaire">Recevoir un nouveau lien</button>
  </form>
</div>
@endsection
