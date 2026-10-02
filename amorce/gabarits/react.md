# {{nom}}

Projet préparé avec la charte graphique de l'administration burkinabè.

    https://chartegraphique.gov.bf

## Conditions d'emploi

Cette charte porte les armoiries du Burkina Faso, le drapeau national et la
devise de l'État. Elle s'emploie pour un service de l'administration
burkinabè ou pour le compte de celle-ci. Les emblèmes ne se retouchent pas :
ni leurs couleurs, ni leurs proportions, ni leur composition.

## Mise en route

Depuis votre point d'entrée, `src/main.jsx` ou `src/main.tsx` :

```js
import { amorcerCharte } from './charte';

amorcerCharte();
```

Ou, si vous préférez tenir le cycle de vie :

```jsx
useEffect(() => {
  Faso.observer();
  return () => Faso.observer(false);
}, []);
```

## Deux points propres à React

Les annonces de la charte se lisent par une référence et un écouteur, non
par une propriété `onQuelqueChose` : React ne relaie pas les événements
personnalisés sur un élément natif.

Les valeurs que la charte écrit dans un champ déclenchent bien `onChange` :
elle passe par l'accesseur du prototype, que React n'a pas remplacé.

Le détail figure dans la page Intégration, section React.
