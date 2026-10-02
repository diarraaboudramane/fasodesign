# {{nom}}

Projet préparé avec la charte graphique de l'administration burkinabè.

    https://chartegraphique.gov.bf

## Conditions d'emploi

Cette charte porte les armoiries du Burkina Faso, le drapeau national et la
devise de l'État. Elle s'emploie pour un service de l'administration
burkinabè ou pour le compte de celle-ci. Les emblèmes ne se retouchent pas :
ni leurs couleurs, ni leurs proportions, ni leur composition.

## Mise en route

Dans `resources/js/app.js` :

```js
import './charte';
```

Puis, dans votre gabarit Blade :

```blade
@vite(['resources/js/app.js'])
```

## Un point propre à Laravel

Avec Livewire, posez `wire:ignore` sur ce que la charte anime et `wire:key`
sur ce qui se répète : Livewire transforme le balisage au lieu de recharger,
et efface les attributs posés après le rendu.

Le détail figure dans la page Intégration, section Laravel.
