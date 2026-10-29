{{-- Courriel en texte brut : aucune valeur n'y est interprétée comme du HTML. --}}
Bonjour {!! $analyse->user->name !!},

@if ($analyse->etat === 'terminee')
L'analyse de {!! $analyse->cible !!} est terminée.

Niveau : {!! $analyse->libelleNiveau() !!}
Règles non respectées : {!! $analyse->bloquants !!} bloquante(s), {!! $analyse->majeurs !!} majeure(s), {!! $analyse->mineurs !!} mineure(s).
@else
L'analyse de {!! $analyse->cible !!} n'a pas pu aboutir.

{!! $analyse->erreur !!}
@endif

Le bilan complet : {!! route('conformite.bilan', $analyse) !!}

Ce bilan constate le respect des règles vérifiables automatiquement, à une date donnée, sur ce qui a été soumis. Il ne vaut ni audit d'accessibilité ni homologation.

L'équipe de la charte graphique
