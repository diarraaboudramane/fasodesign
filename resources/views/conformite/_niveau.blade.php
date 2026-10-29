@php
  $classe = match ($analyse->niveau) {
      'conforme' => 'fs-statut--succes',
      'reserves' => 'fs-statut--alerte',
      'non_conforme' => 'fs-statut--rejet',
      default => $analyse->etat === 'echec' ? 'fs-statut--rejet' : 'fs-statut--cours',
  };
@endphp
<span class="fs-statut {{ $classe }}">{{ $analyse->libelleNiveau() }}</span>
