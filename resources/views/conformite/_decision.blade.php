<form method="post" action="{{ route('conformite.comptes.modifier', $compte) }}">
  @csrf
  <input type="hidden" name="statut" value="{{ $statut }}">
  <input type="hidden" name="role" value="{{ $role }}">
  <button type="submit" class="fs-btn {{ $classe }} fs-btn--sm">{{ $libelle }}</button>
</form>
