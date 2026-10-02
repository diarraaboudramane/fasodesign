# {{nom}}

Projet préparé avec la charte graphique de l'administration burkinabè.

    https://chartegraphique.gov.bf

## Conditions d'emploi

Cette charte porte les armoiries du Burkina Faso, le drapeau national et la
devise de l'État. Elle s'emploie pour un service de l'administration
burkinabè ou pour le compte de celle-ci. Les emblèmes ne se retouchent pas :
ni leurs couleurs, ni leurs proportions, ni leur composition.

## Mise en route

Dans `src/main.ts` :

```ts
import { amorcerCharte } from './charte';

amorcerCharte();
```

Si l'application est rendue côté serveur, amorcez plutôt depuis le
composant racine, une fois le navigateur atteint :

```ts
ngOnInit(): void {
  if (isPlatformBrowser(this.plateforme)) Faso.observer();
}
```

## Un point propre à Angular

Chaque annonce porte deux noms. Un gabarit Angular ne peut pas lier un
événement dont le nom contient deux-points : `(fs:onglet)` y désignerait la
cible « fs ». Employez la forme à trait d'union, `(fs-onglet)`.

Le détail figure dans la page Intégration, section Angular.
