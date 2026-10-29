@extends('layouts.conformite', ['titre' => 'Bilan de conformité'])

@php
  $p = $analyse->perimetre ?? [];
  $parStatut = collect($analyse->resultats ?? [])->groupBy('statut');
  $signalements = $analyse->signalements()->latest()->get()->keyBy(fn ($s) => $s->regle.'|'.$s->ou);
  $badge = ['bloquante' => 'fs-badge--danger', 'majeure' => 'fs-badge--alerte', 'mineure' => 'fs-badge--info'];
@endphp

@if (in_array($analyse->etat, ['attente', 'en_cours'], true))
@push('tete')
{{-- La page se recharge seule tant que l'analyse tourne : pas de script. --}}
<meta http-equiv="refresh" content="5">
@endpush
@endif

@section('contenu')
<div class="fs-espace-grille">
  <div class="fs-espace-ariane">
    <nav class="fs-ariane" aria-label="Fil d'Ariane">
      <ol>
        <li><a href="{{ route('conformite.espace') }}">Mes analyses</a></li>
        <li><span aria-current="page">Bilan n°&nbsp;{{ $analyse->id }}</span></li>
      </ol>
    </nav>
@if ($analyse->etat === 'terminee')
    <div class="fs-actions">
      <a class="fs-btn fs-btn--secondaire fs-btn--sm" href="{{ route('conformite.markdown', $analyse) }}">Télécharger en Markdown</a>
      <button type="button" class="fs-btn fs-btn--secondaire fs-btn--sm" data-imprimer>Imprimer ou enregistrer en PDF</button>
    </div>
@endif
  </div>

  @include('conformite._menu')

  <div class="fs-espace-contenu fs-pile fs-pile--large">
    <div>
      <h1>Bilan de conformité</h1>
      <dl class="fs-definitions">
        <div><dt>Cible</dt><dd><code>{{ $analyse->cible }}</code></dd></div>
        <div><dt>Mode</dt><dd>{{ $analyse->libelleMode() }}</dd></div>
        <div><dt>Demandée le</dt><dd>{{ $analyse->created_at->format('d/m/Y à H:i') }}</dd></div>
        <div><dt>Charte de référence</dt><dd>{{ $analyse->version_charte }}</dd></div>
        <div><dt>Niveau</dt><dd>@include('conformite._niveau')</dd></div>
@if ($analyse->etat === 'terminee')
        <div><dt>Règles non respectées</dt><dd>{{ $analyse->bloquants }} bloquante(s), {{ $analyse->majeurs }} majeure(s), {{ $analyse->mineurs }} mineure(s)</dd></div>
        <div><dt>Analysé</dt><dd>{{ $p['pages'] ?? 0 }} page(s) HTML, {{ $p['fichiers'] ?? 0 }} fichier(s) en tout</dd></div>
@endif
      </dl>
    </div>

@if (in_array($analyse->etat, ['attente', 'en_cours'], true))
    <div class="fs-encart fs-encart--info" role="status" aria-busy="true">
      <span class="fs-encart-marque fs-icone fs-icone--horloge" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre fs-rangee"><span class="fs-sablier" aria-hidden="true"></span> Analyse en cours</p>
        <p>L'outil lit les pages et les fichiers de la cible. Cette page se met à jour toute seule&nbsp;; vous recevrez aussi un courriel quand le bilan sera prêt.</p>
      </div>
    </div>
@elseif ($analyse->etat === 'echec')
    <div class="fs-encart fs-encart--danger" role="alert">
      <span class="fs-encart-marque fs-icone fs-icone--erreur" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">L'analyse n'a pas pu aboutir</p>
        <p>{{ $analyse->erreur }}</p>
        <p><a href="{{ route('conformite.espace') }}">Lancer une nouvelle analyse</a></p>
      </div>
    </div>
@else

@if ($comparaison)
    <div class="fs-encart fs-encart--info">
      <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">Depuis l'analyse du {{ $comparaison['precedente']->created_at->format('d/m/Y') }}</p>
        <p>{{ count($comparaison['corriges']) }} écart(s) corrigé(s), {{ count($comparaison['nouveaux']) }} nouvel(s) écart(s). <a href="{{ route('conformite.bilan', $comparaison['precedente']) }}">Voir le bilan précédent</a>.</p>
      </div>
    </div>
@endif

@foreach (['non_respectee' => 'Écarts à corriger', 'non_verifiable' => 'Non vérifiable par l\'outil', 'respectee' => 'Règles respectées'] as $statut => $titre)
@continue (! $parStatut->has($statut))
    <section aria-labelledby="titre-{{ $statut }}">
      <h2 id="titre-{{ $statut }}">{{ $titre }}</h2>

@foreach ($parStatut[$statut]->sortBy(fn ($r) => array_search($r['gravite'], ['bloquante', 'majeure', 'mineure'])) as $r)
      <div class="fs-carte fs-mt-4">
        <div class="fs-carte-corps">
          <h3 class="fs-carte-titre">
            {{ $r['titre'] }}
            @if ($statut === 'non_respectee')<span class="fs-badge {{ $badge[$r['gravite']] }}">{{ \App\Conformite\BilanMarkdown::GRAVITES[$r['gravite']] }}</span>@endif
          </h3>
@if ($statut === 'non_respectee')
          <p>{{ $r['correction'] }} <a href="{{ \App\Conformite\Reference::adresse($r['lien']) }}">Ce que dit la charte</a>.</p>
@endif
@if ($r['note'])
          <p class="fs-legende">{{ $r['note'] }}</p>
@endif
@if ($r['ecarts'])
          <div class="fs-tableau-cadre fs-mt-4">
            <table class="fs-tableau fs-tableau--compact">
              <caption>{{ $r['occurrences'] }} occurrence(s){{ $r['occurrences'] > count($r['ecarts']) ? ', les '.count($r['ecarts']).' premières affichées' : '' }}</caption>
              <thead><tr><th>Où</th><th>Constat</th><th class="fs-nowrap"><span class="fs-invisible">Signaler</span></th></tr></thead>
              <tbody>
@foreach ($r['ecarts'] as $e)
                <tr>
                  <td><code>{{ $e['ou'] }}</code></td>
                  <td>{{ $e['extrait'] }}</td>
                  <td>
@php $signale = $signalements->get($r['id'].'|'.mb_substr($e['ou'], 0, 255)); @endphp
@if ($signale)
                    {{-- Déjà signalé : l'usager voit où en est son signalement, et la réponse de l'équipe. --}}
                    <span class="fs-statut {{ ['nouveau' => 'fs-statut--cours', 'corrige' => 'fs-statut--succes', 'maintenu' => 'fs-statut--attente'][$signale->statut] }}">{{ $signale->statut === 'nouveau' ? 'Signalé' : \App\Models\Signalement::STATUTS[$signale->statut] }}</span>
                    @if ($signale->reponse)<p class="fs-legende">{{ $signale->reponse }}</p>@endif
@else
                    <details>
                      <summary class="fs-btn fs-btn--fantome fs-btn--sm" title="Cet écart vous paraît faux : signalez-le à l'équipe de la charte">Signaler</summary>
                      <form method="post" action="{{ route('conformite.signaler', $analyse) }}" class="fs-pile fs-mt-4">
                        @csrf
                        <input type="hidden" name="regle" value="{{ $r['id'] }}">
                        <input type="hidden" name="ou" value="{{ mb_substr($e['ou'], 0, 255) }}">
                        <label class="fs-label" for="commentaire-{{ $loop->parent->index }}-{{ $loop->index }}">Pourquoi cet écart vous paraît-il faux&nbsp;?</label>
                        <textarea class="fs-zone" id="commentaire-{{ $loop->parent->index }}-{{ $loop->index }}" name="commentaire" maxlength="1000" rows="3"></textarea>
                        <button type="submit" class="fs-btn fs-btn--secondaire fs-btn--sm">Envoyer le signalement</button>
                      </form>
                    </details>
@endif
                  </td>
                </tr>
@endforeach
              </tbody>
            </table>
          </div>
@endif
        </div>
      </div>
@endforeach
    </section>
@endforeach

    <section aria-labelledby="titre-hors-portee">
      <h2 id="titre-hors-portee">Ce qui n'a pas été vérifié</h2>
      <ul class="fs-liste-puces">
@foreach ($p['hors_de_portee'] ?? [] as $point)
        <li>{{ $point }}</li>
@endforeach
@if (! empty($p['sans_javascript']))
        <li>Le contenu ajouté par JavaScript&nbsp;: les pages sont lues sans exécuter leurs scripts. Une application rendue entièrement dans le navigateur paraît donc presque vide.</li>
@endif
@foreach ($p['ignores'] ?? [] as $raison => $nombre)
        <li>{{ $nombre }} {{ $raison }}, ignorés.</li>
@endforeach
      </ul>
@if (! empty($p['non_atteints']))
      <details class="fs-accordeon fs-mt-4">
        <summary>{{ count($p['non_atteints']) }} page(s) ou fichier(s) non atteint(s)</summary>
        <ul class="fs-liste-puces">
@foreach ($p['non_atteints'] as $absent)
          <li><code>{{ $absent }}</code></li>
@endforeach
        </ul>
      </details>
@endif
    </section>
@endif
  </div>

  <aside class="fs-espace-actions" aria-label="Portée du bilan">
    <div class="fs-encart fs-encart--alerte">
      <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">Portée du bilan</p>
        <p>Il constate le respect des règles vérifiables automatiquement, à une date donnée, sur ce qui a été soumis. Il ne vaut ni audit d'accessibilité ni homologation.</p>
      </div>
    </div>
  </aside>
</div>
<script src="{{ ressource('assets/js/conformite.js') }}" defer></script>
@endsection
