{{-- Menu principal de l'espace, à gauche, comme le prévoit la disposition de l'espace privé. --}}
<nav class="fs-espace-menu fs-sommaire" aria-label="Menu de l'outil">
  <p class="fs-sommaire-titre">Contrôle de conformité</p>
  <ul>
    <li><a href="{{ route('conformite.espace') }}"@if (request()->routeIs('conformite.espace')) aria-current="true"@endif>Nouvelle analyse</a></li>
    <li><a href="{{ route('conformite.espace') }}#precedentes"@if (request()->routeIs('conformite.bilan')) aria-current="true"@endif>Mes analyses</a></li>
@if (auth()->user()->estAdministrateur())
    <li><a href="{{ route('conformite.comptes') }}"@if (request()->routeIs('conformite.comptes')) aria-current="true"@endif>Comptes</a></li>
    <li><a href="{{ route('conformite.signalements') }}"@if (request()->routeIs('conformite.signalements')) aria-current="true"@endif>Écarts signalés
      @php $aTraiter = \App\Models\Signalement::where('statut', 'nouveau')->count(); @endphp
      @if ($aTraiter)<span class="fs-badge fs-badge--alerte">{{ $aTraiter }}<span class="fs-invisible"> à traiter</span></span>@endif
    </a></li>
@endif
    <li><a href="{{ route('conformite') }}">Règles vérifiées</a></li>
  </ul>
</nav>
