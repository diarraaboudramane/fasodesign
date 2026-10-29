{{-- Un champ de saisie conforme à la charte : libellé, astérisque des champs
     obligatoires, aide facultative, message d'erreur relié au champ. --}}
<div class="fs-champ" @error($nom) data-etat="erreur" @enderror>
  <label class="fs-label" for="{{ $nom }}">
    {{ $libelle }} @if ($requis ?? true)<span class="fs-requis" aria-hidden="true">*</span>@endif
  </label>
  @isset($aide)<p class="fs-aide" id="{{ $nom }}-aide">{{ $aide }}</p>@endisset
  <input class="fs-saisie" type="{{ $type ?? 'text' }}" id="{{ $nom }}" name="{{ $nom }}"
         @if (($type ?? 'text') !== 'password') value="{{ old($nom, $valeur ?? '') }}" @endif
         autocomplete="{{ $remplissage ?? 'off' }}" @if ($requis ?? true) required @endif
         @isset($aide) aria-describedby="{{ $nom }}-aide" @endisset
         @error($nom) aria-invalid="true" aria-describedby="{{ $nom }}-erreur" @enderror>
  @error($nom)
    <p class="fs-message fs-message--erreur" id="{{ $nom }}-erreur">{{ $message }}</p>
  @enderror
</div>
