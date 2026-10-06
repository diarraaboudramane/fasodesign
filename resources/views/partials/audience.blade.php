<p data-audience="{{ route('audience', absolute: false) }}">
        <span data-audience-en-ligne>{{ $en_ligne }}&nbsp;{{ $en_ligne > 1 ? 'personnes' : 'personne' }} en ligne</span>
        ·
        <span data-audience-mois>{{ $mois }}&nbsp;{{ $mois > 1 ? 'visiteurs' : 'visiteur' }} en {{ $libelle_mois }}</span>
      </p>
