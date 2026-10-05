<form class="fs-champ" role="search" method="get" action="{{ route('recherche') }}">
  {{-- Libellé masqué à l'écran, jamais retiré : le lecteur d'écran l'annonce toujours. --}}
  <label class="{{ ($libelleMasque ?? false) ? 'fs-invisible' : 'fs-label' }}" for="{{ $id }}">Rechercher dans la charte</label>
  <div class="fs-recherche">
    <input class="fs-saisie" type="search" id="{{ $id }}" name="q" value="{{ $valeur ?? '' }}"
           placeholder="Bouton, couleurs, contraste, Laravel…" maxlength="200" required>
    <button type="submit" class="fs-btn fs-btn--principal">Rechercher</button>
  </div>
</form>
