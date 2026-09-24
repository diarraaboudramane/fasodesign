# Charte graphique de l'administration burkinabè

Système de conception des services numériques de l'État burkinabè : jetons,
composants CSS, comportements sans dépendance, gabarits institutionnels.

Sans dépendance, sans étape de compilation. Les fichiers se servent tels quels
ou s'installent comme une dépendance ordinaire.

---

## Installation

```bash
npm install @gouv-bf/charte-graphique
```

Selon le dépôt retenu par l'administration, le projet déclare l'espace de noms —
et lui seul, le reste des dépendances continuant d'aller là où il allait :

```ini
# .npmrc, à la racine du projet
@gouv-bf:registry=https://depot.fasodesign.gov.bf/
```

Les cinq dépôts possibles et leurs implications sont comparés dans la page
Intégration, section « Publier et servir en ligne ».

Puis, dans le projet :

```js
import '@gouv-bf/charte-graphique/css/tokens.css';
import '@gouv-bf/charte-graphique/css/faso.css';
import '@gouv-bf/charte-graphique/css/icones.css';
import Faso from '@gouv-bf/charte-graphique';

Faso.observer();   // une fois, au démarrage de l'application
```

Servie telle quelle, sans chaîne Node, l'initialisation est automatique :

```html
<script src="/charte/js/faso-amorce.js"></script>
<link rel="stylesheet" href="/charte/css/tokens.css">
<link rel="stylesheet" href="/charte/css/faso.css">
<link rel="stylesheet" href="/charte/css/icones.css">
<script src="/charte/js/faso.js" defer></script>
```

Les trois feuilles sont obligatoires et solidaires : plusieurs composants
puisent leurs repères dans `icones.css`.

---

## Ce que contient le paquet

| Chemin | Contenu |
| --- | --- |
| `@gouv-bf/charte-graphique` | Comportements, avec leurs déclarations TypeScript |
| `…/css/tokens.css` | 193 jetons, thèmes clair et sombre, déclarations de polices |
| `…/css/faso.css` | 65 composants |
| `…/css/icones.css` | 35 icônes, appliquées en masque CSS |
| `…/amorce` | Pose le thème retenu avant le premier rendu |
| `…/jetons.json` | Les jetons, valeurs résolues, pour les outils de conception |
| `…/img/armoiries.svg` | Emblème vectoriel |
| `…/polices/*` | Archivo et Inter, variables, sous-ensemble latin, licences comprises |
| `…/android/*` | Ressources Android produites depuis les mêmes jetons |

---

## Le partage des responsabilités

La charte fournit **l'apparence et les comportements des composants isolés**.
Les composants pilotés par des données — tableau trié, filtré, paginé,
autocomplétion, export — restent la responsabilité du cadriciel dès lors qu'il
ne rend qu'une partie des lignes.

Quatre environnements sont traités en détail dans la documentation :
**Angular**, **React**, **Laravel** et **Android**. Les autres se ramènent à
l'un des quatre.

Trois points sont propres à un environnement :

- **React** — les annonces se lisent par une référence et un écouteur, non par
  une propriété `onQuelqueChose`. Les valeurs que la charte écrit dans un champ
  déclenchent bien `onChange` : elle passe par l'accesseur du prototype.
- **Angular** — chaque annonce porte un second nom à trait d'union,
  `fs-onglet`, seul liable dans un gabarit.
- **Laravel** — avec Livewire, `wire:ignore` sur ce que la charte anime et
  `wire:key` sur ce qui se répète.

---

## Ce que la charte garantit

- **Surface publique sans DOM** : les 28 points d'entrée sont appelables au
  rendu côté serveur sans exception ni effet.
- **Initialisation idempotente** : quatre appels posent exactement autant de
  gestionnaires qu'un seul.
- **Tout état écrit est annulable** par `preventDefault()` sur l'annonce qui le
  précède.
- **Aucun écouteur par instance sur `document`** : rien ne retient un nœud
  détruit par le cadriciel.
- **Extraction inoffensive** : aucune cellule de texte ne peut être interprétée
  comme une formule par un tableur.
- **Aucun style ni script en ligne** : pas de `'unsafe-inline'`.
- **Aucune dépendance, aucun appel réseau.**

---

## Accessibilité

Le niveau visé est WCAG 2.1 AA. Les 118 couples de couleurs effectivement
employés par la feuille de styles passent leur seuil dans les deux thèmes, et
c'est vérifié par `npm test`.

Ce qui n'a pas été fait est écrit aussi : aucun essai avec un lecteur d'écran
réel, aucun audit par un tiers, aucun test avec des personnes en situation de
handicap. La formule juste est donc « conçu pour WCAG 2.1 AA, vérifié par la
mesure sur les points listés », et non « conforme ».

---

## Vérification

```bash
npm test
```

Syntaxe, balisage des pages, classes orphelines, bonne formation des SVG et des
ressources Android, correspondance des jetons, cohérence du paquet, contraste,
et six suites de comportements. C'est ce qui doit passer avant chaque
publication.

```bash
npm run jetons     # régénère assets/jetons.json depuis tokens.css
npm run android    # régénère android/ depuis tokens.css
```

`tokens.css` est la source faisant foi : les deux autres en sont des
projections, jamais modifiées à la main.

---

## Langue des libellés

Une vingtaine de libellés sont écrits par les comportements eux-mêmes. Ils sont
rassemblés dans un objet unique, à compléter avant l'initialisation :

```js
Object.assign(Faso.textes, { langue: 'fr-BF', pageSuivante: '…' });
```

Le pays compte une soixantaine de langues : la charte ne pouvait pas en imposer
une. Les fichiers de police livrés couvrent en revanche le seul français ; les
caractères ɛ, ɔ, ŋ, ɩ et ʋ dépendent aujourd'hui d'une police de repli, et la
documentation dit ce qu'il faudrait faire pour y remédier.

---

## Emblèmes

L'emblème, le filet et le repère nationaux sont des symboles de l'État. Leurs
proportions, leurs teintes et leur composition ne se retouchent pas. Les
couleurs officielles sont verrouillées à leur palier dans la feuille de jetons,
y compris en thème sombre.

---

## Licence

Voir `LISEZMOI-PAQUET.txt`. Les conditions précises de réutilisation relèvent de
l'autorité de tutelle du système, qui reste à désigner : **ce point doit être
arrêté avant toute publication sur un registre public**.

Les polices Archivo et Inter sont distribuées sous licence SIL Open Font
License 1.1, dont le texte accompagne les fichiers.
