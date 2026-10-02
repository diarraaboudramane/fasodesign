# {{nom}}

Projet préparé avec la charte graphique de l'administration burkinabè.

    https://chartegraphique.gov.bf

## Conditions d'emploi

Cette charte porte les armoiries du Burkina Faso, le drapeau national et la
devise de l'État. Elle s'emploie pour un service de l'administration
burkinabè ou pour le compte de celle-ci. Les emblèmes ne se retouchent pas :
ni leurs couleurs, ni leurs proportions, ni leur composition.

## Mise en route

Ouvrez `index.html`, ou servez le dossier :

    npx --yes serve .

Aucun script n'est à appeler : la charte s'initialise au chargement de la
page.

## Mise en ligne

Les fichiers de `node_modules/@govbf/fasodesign/assets` se recopient tels
quels dans le dossier public du serveur. Conservez l'arborescence `css/` et
`polices/` : les déclarations de polices désignent leurs fichiers par un
chemin relatif.

La configuration du serveur, avec les en-têtes de cache et de sécurité, est
fournie dans le dépôt de la charte, dossier `hebergement/`.
