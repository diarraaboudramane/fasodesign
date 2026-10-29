@extends('layouts.conformite', ['titre' => 'Contrôle de conformité'])

@section('contenu')
<div class="fs-mesure">
  <span class="fs-surtitre">Pour les équipes des ministères et des institutions</span>
  <h1>Contrôle de conformité à la charte graphique</h1>
  <p class="fs-chapeau">
    Soumettez l'adresse de votre site ou l'archive ZIP de son code&nbsp;: l'outil vérifie
    les règles de la charte qu'une machine sait contrôler, et vous rend un bilan qui
    dit, pour chaque écart, quoi corriger et où.
  </p>

  <div class="fs-actions fs-mt-6">
@auth
    <a class="fs-btn fs-btn--principal fs-btn--lg" href="{{ route('conformite.espace') }}">Lancer une analyse</a>
@else
    <a class="fs-btn fs-btn--principal fs-btn--lg" href="{{ route('connexion') }}">Se connecter</a>
    <a class="fs-btn fs-btn--secondaire fs-btn--lg" href="{{ route('inscription') }}">Créer un compte</a>
@endauth
  </div>

  <h2 class="fs-mt-8">Deux façons de soumettre</h2>
  <dl class="fs-definitions">
    <div>
      <dt>Par l'adresse</dt>
      <dd>L'outil parcourt les pages publiques du site, {{ config('charte.conformite.pages_max') }} au plus, avec leurs feuilles de style et leurs scripts. Les pages réservées aux usagers connectés lui échappent, et il n'exécute pas le JavaScript.</dd>
    </div>
    <div>
      <dt>Par l'archive ZIP</dt>
      <dd>L'outil lit le code source, {{ intdiv(config('charte.conformite.archive_max_ko'), 1024) }} Mo au plus, sans jamais l'exécuter ni l'installer. Il voit les pages réservées aux usagers connectés, mais pas le rendu final. L'archive est supprimée dès la fin de l'analyse.</dd>
    </div>
  </dl>

  <h2 class="fs-mt-8">Ce qui est vérifié</h2>
  <div class="fs-tableau-cadre">
    <table class="fs-tableau">
      <caption>Règles appliquées par l'outil</caption>
      <thead><tr><th>Règle</th><th>Gravité</th></tr></thead>
      <tbody>
@foreach (\App\Conformite\Moteur::regles() as $regle)
        <tr>
          <td><a href="{{ \App\Conformite\Reference::adresse($regle->lien()) }}">{{ $regle->titre() }}</a></td>
          <td>{{ \App\Conformite\BilanMarkdown::GRAVITES[$regle->gravite()] }}</td>
        </tr>
@endforeach
      </tbody>
    </table>
  </div>

  <div class="fs-encart fs-encart--info fs-mt-8">
    <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
    <div class="fs-encart-corps">
      <p class="fs-encart-titre">Ce que le bilan ne dit pas</p>
      <p>
        Une machine ne juge ni la clarté d'un texte, ni l'usage réel au clavier ou avec un
        lecteur d'écran. Chaque bilan le rappelle&nbsp;: il ne vaut ni audit d'accessibilité
        ni homologation.
      </p>
    </div>
  </div>
</div>
@endsection
