{{-- Courriel en texte brut : rien n'y est interprété comme du HTML, les
     valeurs sont donc écrites sans échappement d'entités. --}}
Message reçu par le formulaire de contact de la documentation.

Nom : {!! $donnees['nom'] !!}
Adresse : {!! $donnees['courriel'] !!}
Objet : {!! $donnees['objet'] !!}

{!! $donnees['message'] !!}
