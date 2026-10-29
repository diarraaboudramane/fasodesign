@extends('layouts.conformite', ['titre' => 'Écarts signalés'])

@section('contenu')
<div class="fs-espace-grille">
  <div class="fs-espace-ariane">
    <nav class="fs-ariane" aria-label="Fil d'Ariane">
      <ol>
        <li><a href="{{ route('conformite.espace') }}">Contrôle de conformité</a></li>
        <li><span aria-current="page">Écarts signalés</span></li>
      </ol>
    </nav>
  </div>

  @include('conformite._menu')

  <div class="fs-espace-contenu fs-pile fs-pile--large">
    <div>
      <h1>Écarts signalés</h1>
      <p class="fs-attenue">
        Des écarts que les usagers estiment erronés. Chacun dit si une règle se trompe&nbsp;:
        corrigez la règle, ou maintenez l'écart en expliquant pourquoi. La réponse
        apparaît dans le bilan de l'usager.
      </p>
    </div>

    @include('conformite._erreurs')

    <nav class="fs-onglets" aria-label="Filtrer par statut">
@foreach (['nouveau' => 'À traiter', 'corrige' => 'Règle corrigée', 'maintenu' => 'Écart maintenu', 'tous' => 'Tous'] as $valeur => $libelle)
      <a class="fs-onglet" href="{{ route('conformite.signalements', ['statut' => $valeur]) }}" @if ($statut === $valeur) aria-current="page" aria-selected="true" @endif>
        {{ $libelle }}
        @if ($valeur !== 'tous')<span class="fs-legende">({{ $comptes[$valeur] ?? 0 }})</span>@endif
      </a>
@endforeach
    </nav>

@if ($signalements->isEmpty())
    <p class="fs-attenue">Aucun signalement dans cette catégorie.</p>
@endif

@foreach ($signalements as $s)
@php
  $regle = $s->regleContestee();
  $badge = ['bloquante' => 'fs-badge--danger', 'majeure' => 'fs-badge--alerte', 'mineure' => 'fs-badge--info'];
@endphp
    <article class="fs-carte">
      <div class="fs-carte-corps fs-pile">
        <div class="fs-rangee fs-rangee--entre">
          <h2 class="fs-carte-titre">
            {{ $regle?->titre() ?? $s->regle }}
            @if ($regle)<span class="fs-badge {{ $badge[$regle->gravite()] }}">{{ \App\Conformite\BilanMarkdown::GRAVITES[$regle->gravite()] }}</span>@endif
          </h2>
          <span class="fs-statut {{ ['nouveau' => 'fs-statut--cours', 'corrige' => 'fs-statut--succes', 'maintenu' => 'fs-statut--attente'][$s->statut] }}">{{ \App\Models\Signalement::STATUTS[$s->statut] }}</span>
        </div>

        <dl class="fs-definitions">
          <div><dt>Signalé le</dt><dd>{{ $s->created_at->format('d/m/Y à H:i') }}</dd></div>
          <div><dt>Par</dt><dd>{{ $s->auteur?->name ?? 'Compte supprimé' }}@if ($s->auteur) <span class="fs-faible">— {{ $s->auteur->entite }}, {{ $s->auteur->email }}</span>@endif</dd></div>
          <div><dt>Cible analysée</dt><dd><code>{{ $s->analyse->cible }}</code> <span class="fs-faible">({{ $s->analyse->libelleMode() }}, charte {{ $s->analyse->version_charte }})</span></dd></div>
          <div><dt>Bilan</dt><dd><a href="{{ route('conformite.bilan', $s->analyse) }}">Bilan n°&nbsp;{{ $s->analyse_id }}</a> du {{ $s->analyse->created_at->format('d/m/Y') }}</dd></div>
          <div><dt>Règle</dt><dd><code>{{ $s->regle }}</code>@if ($regle) — <a href="{{ \App\Conformite\Reference::adresse($regle->lien()) }}">ce que dit la charte</a>@endif</dd></div>
          <div><dt>Où</dt><dd><code>{{ $s->ou }}</code></dd></div>
          <div><dt>Constat de l'outil</dt><dd>{{ $s->constat() ?: '—' }}</dd></div>
          <div><dt>Commentaire de l'usager</dt><dd>{{ $s->commentaire ?: 'Aucun commentaire.' }}</dd></div>
@if ($s->statut !== 'nouveau')
          <div><dt>Traité</dt><dd>le {{ $s->traite_le?->format('d/m/Y') }} par {{ $s->traitePar?->name ?? 'un administrateur' }}</dd></div>
@if ($s->reponse)
          <div><dt>Réponse</dt><dd>{{ $s->reponse }}</dd></div>
@endif
@endif
        </dl>

@if ($s->statut === 'nouveau')
        <form method="post" action="{{ route('conformite.signalements.traiter', $s) }}" class="fs-pile" novalidate>
          @csrf
          <div class="fs-champ">
            <label class="fs-label" for="reponse-{{ $s->id }}">Réponse à l'usager</label>
            <p class="fs-aide" id="reponse-{{ $s->id }}-aide">Obligatoire pour maintenir l'écart : dites ce que la règle vérifie et pourquoi elle s'applique ici.</p>
            <textarea class="fs-zone" id="reponse-{{ $s->id }}" name="reponse" maxlength="1000" aria-describedby="reponse-{{ $s->id }}-aide"></textarea>
          </div>
          <div class="fs-actions">
            <button type="submit" name="statut" value="corrige" class="fs-btn fs-btn--principal fs-btn--sm">La règle est corrigée</button>
            <button type="submit" name="statut" value="maintenu" class="fs-btn fs-btn--secondaire fs-btn--sm">Maintenir l'écart</button>
          </div>
        </form>
@endif
      </div>
    </article>
@endforeach

    {{ $signalements->links('conformite._pagination') }}
  </div>

  <aside class="fs-espace-actions" aria-label="Règles les plus contestées">
    <div class="fs-carte">
      <div class="fs-carte-corps">
        <h2 class="fs-carte-titre">Règles à revoir en premier</h2>
@if ($parRegle->isEmpty())
        <p class="fs-attenue">Aucun signalement à traiter.</p>
@else
        <ul class="fs-liste-separee">
@foreach ($parRegle as $id => $nombre)
          <li><code>{{ $id }}</code><br><span class="fs-faible">{{ $nombre }} signalement(s) à traiter</span></li>
@endforeach
        </ul>
@endif
      </div>
    </div>
  </aside>
</div>
@endsection
