# Charte graphique de l'administration burkinabè

Système de conception des services numériques de l'État burkinabè : jetons,
composants CSS, comportements sans dépendance, gabarits institutionnels.

Sans dépendance, sans étape de compilation. Les fichiers se servent tels quels
ou s'installent comme une dépendance ordinaire.

---

## Installation

Pour un projet qui démarre, une commande écrit les fichiers de départ et
installe la charte :

```bash
npm create @govbf/fasodesign@latest
```

Elle demande le dossier, le type de projet — `statique`, `react`, `angular` ou
`laravel` — et le dépôt npm interne si le service en emploie un. Tout peut se
donner sur la ligne de commande :

```bash
npm create @govbf/fasodesign@latest mon-service -- \
  --type=react --depot=https://depot.chartegraphique.gov.bf/ --oui
```

La charte porte les armoiries, le drapeau et la devise de l'État : la commande
rappelle les conditions d'emploi et demande de les accepter avant d'installer
quoi que ce soit. Sur une chaîne d'intégration, `FASODESIGN_ACCEPTE_LICENCE=oui`.

Pour un projet déjà commencé :

```bash
npm install @govbf/fasodesign
```

Selon le dépôt retenu par l'administration, le projet déclare l'espace de noms —
et lui seul, le reste des dépendances continuant d'aller là où il allait :

```ini
# .npmrc, à la racine du projet
@govbf:registry=https://depot.chartegraphique.gov.bf/
```

Les cinq dépôts possibles et leurs implications sont comparés dans la page
Intégration, section « Publier et servir en ligne ».

Puis, dans le projet :

```js
import '@govbf/fasodesign/css/tokens.css';
import '@govbf/fasodesign/css/faso.css';
import '@govbf/fasodesign/css/icones.css';
import Faso from '@govbf/fasodesign';

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
| `@govbf/fasodesign` | Comportements, avec leurs déclarations TypeScript |
| `…/css/tokens.css` | 193 jetons, thèmes clair et sombre, déclarations de polices |
| `…/css/faso.css` | 65 composants |
| `…/css/icones.css` | 35 icônes, appliquées en masque CSS |
| `…/amorce` | Pose le thème retenu avant le premier rendu |
| `…/jetons.json` | Les jetons, valeurs résolues, pour les outils de conception |
| `…/img/armoiries.svg` | Grand format, à partir de 64 px |
| `…/img/armoiries-moyen.svg` | Moyen format, 32 à 64 px, un tiers plus léger |
| `…/img/armoiries-ecu.svg` | Petit format, 16 à 32 px : l'écu seul |
| `…/img/armoiries-gris.svg` | Impression en une seule encre, photocopie |
| `…/img/armoiries-512.png` … `-64.png` | Bureautique, courriel, icône d'application |
| `…/img/favicon.svg` | Mini format, 16 px et en dessous : le drapeau et son étoile |
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

## La documentation : une application Laravel

Le paquet npm ne change pas : il ne contient ni PHP ni Laravel. C'est le site
de documentation qui est une application Laravel 13 (PHP 8.3 ou plus), sans
base de données. À la première visite, une case reCAPTCHA est demandée avant
toute page ; les pages ne posent ensuite aucun autre cookie que celui qui retient
cette vérification. Seules les pages `/contact` et `/verification` ouvrent une
session.

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan serve          # http://localhost:8000
```

| Chemin | Rôle |
| --- | --- |
| `config/charte.php` | La liste des pages : routes, menus, plan du site et export en sont tirés |
| `resources/views/pages/` | Le contenu de chaque page, en Blade |
| `resources/views/layouts/` | En-tête, menus et pied, communs à toutes les pages |
| `app/Http/Controllers/FichierController.php` | Sert `assets/`, `archives/`, `android/` et la diffusion figée `/2.0.0/…` depuis le dépôt, sans copie dans `public/` |
| `app/Http/Middleware/EnTetesDeSecurite.php` | Politique de sécurité, réserve de fouille, cache |
| `app/Support/Audience.php` | Personnes en ligne (actives depuis 5 minutes) et visiteurs du mois, au pied de l'accueil. Seuls les visiteurs vérifiés comptent ; aucune écriture sur l'appareil, aucune adresse IP gardée, une empreinte hachée qui change chaque mois. `CHARTE_AUDIENCE=false` la coupe |
| `app/Support/Recherche.php` | La recherche de l'accueil (`/recherche?q=…`) : index des sections de toutes les pages, sans accents ni casse, recalculé dès qu'une vue change |

Une page s'ajoute en deux temps : une entrée dans `config/charte.php`, une vue
dans `resources/views/pages/`. Les extraits de code Blade cités dans la
documentation sont entourés de `@verbatim` : sans cela, ils seraient exécutés au
lieu d'être affichés.

### Vérification anti-robot à l'entrée

Toutes les pages HTML de l'application (documentation, recherche, contact)
renvoient d'abord vers `/verification`, où l'usager coche la case reCAPTCHA v2,
puis revient à la page demandée. La case n'est redemandée qu'après un temps
réglable sans aucune page vue (24 heures par défaut, 10 minutes sur le serveur
de test) : chaque page fait repartir le délai, si bien qu'un visiteur qui lit
n'est jamais interrompu. La preuve est un cookie chiffré, `charte_humain`,
réécrit à chaque page, que l'usager ne peut ni forger ni prolonger lui-même
(`app/Http/Middleware/VerifierHumain.php`). Les pages qui le posent sont
marquées `Cache-Control: private, no-store` : aucun cache partagé ne les garde.

Restent ouverts, sans case :

- les fichiers de la charte (`/assets/…`, `/2.0.0/…`), que les services des
  ministères chargent depuis leurs propres pages ;
- `robots.txt`, `sitemap.xml` et `VERSION.txt` ;
- Googlebot, Bingbot et Applebot authentiques : leur adresse doit se résoudre
  dans le domaine du moteur, puis revenir à la même adresse. Un agent qui se
  dit Googlebot depuis une autre adresse reçoit la case. Sans cette exception,
  la documentation sortirait des résultats de recherche.

Réglages : `CHARTE_VERIFICATION=false` coupe la vérification,
`CHARTE_VERIFICATION_DUREE` fixe sa durée en minutes (1440 par défaut). Derrière
un mandataire inverse, déclarer ses adresses comme mandataires de confiance, sans
quoi tous les visiteurs auraient l'adresse du mandataire. Le site exporté,
statique, n'a pas de vérification.

### Formulaire de contact et reCAPTCHA

La page `/contact` envoie un courriel à `CONTACT_DESTINATAIRE`. Elle est
protégée par Google reCAPTCHA v2 (case « Je ne suis pas un robot ») et
vérifiée côté serveur (`app/Rules/Recaptcha.php`). Elle n'est accessible que
par l'application : le site exporté, statique, ne peut pas recevoir de
formulaire.

Avant la mise en production :

1. créer des clés de type « Case à cocher » pour le domaine dans la console
   reCAPTCHA, puis renseigner `RECAPTCHA_SITE_KEY` et `RECAPTCHA_SECRET_KEY` ;
   les clés d'essai de `.env.example` sont refusées en production ;
2. renseigner `CONTACT_DESTINATAIRE` et un vrai serveur de courrier
   (`MAIL_MAILER=smtp`…) : en développement, les messages sont écrits dans
   `storage/logs/laravel.log` ;
3. mettre `SESSION_SECURE_COOKIE=true` derrière HTTPS.

Les origines de Google ne sont ouvertes dans la politique de sécurité que sur
cette page.

Deux façons de mettre en ligne :

- **l'application** : la racine du serveur est `public/`. Apache lit
  `public/.htaccess` ; pour nginx, `hebergement/nginx-laravel.conf` ;
- **le site statique**, sans PHP sur le serveur : `npm run site` fait écrire
  les pages par l'application (`php artisan charte:exporter`), avec des liens
  relatifs, puis assemble le site comme auparavant. C'est ce que publie
  l'intégration continue sur GitHub Pages. Il faut PHP sur le poste qui
  construit le site, pas sur celui qui le sert.

### Déployer l'application sur le serveur de test

Le serveur de test est `https://chartegraphique-21.mtdpce-test.gov.bf`. Le
domaine officiel reste `chartegraphique.gov.bf` pour la bascule.

Sur le serveur (PHP 8.3 ou plus, extensions `ctype`, `curl`, `dom`,
`fileinfo`, `mbstring`, `openssl`, `tokenizer`, `xml`) :

```bash
composer install --no-dev --optimize-autoloader
cp hebergement/serveur-test.env .env      # puis compléter les « À-RENSEIGNER »
php artisan key:generate --force
php artisan charte:diagnostic             # doit finir par « Prêt à recevoir des visiteurs »
php artisan optimize                      # met en cache configuration, routes et vues
```

- La racine web est `public/`, et rien d'autre : le reste du dossier contient
  `.env`. Apache lit `public/.htaccess` ; pour nginx, partir de
  `hebergement/nginx-laravel.conf`.
- Le compte du serveur web doit pouvoir écrire dans `storage/` et
  `bootstrap/cache/`.
- Les clés reCAPTCHA se créent pour `chartegraphique-21.mtdpce-test.gov.bf`.
  Une même clé peut porter plusieurs domaines : à la bascule, il suffit d'y
  ajouter `chartegraphique.gov.bf`.
- Derrière un proxy qui termine le TLS, déclarer son adresse dans
  `MANDATAIRES_DE_CONFIANCE`. Les liens restent en https dans tous les cas,
  dès que `APP_URL` commence par `https://`.
- Après toute modification de `.env`, relancer `php artisan optimize` : la
  configuration est lue depuis le cache, pas depuis le fichier.

Le serveur de test n'est pas indexé (`CHARTE_INDEXABLE=false`) : `robots.txt`
refuse tout et chaque réponse porte `noindex`. À la bascule, changer `APP_URL`
et `MAIL_FROM_ADDRESS`, passer `CHARTE_INDEXABLE=true`, relancer
`charte:diagnostic` puis `optimize`.

---

## Vérification

```bash
composer install   # une fois : les pages sont écrites par l'application
npm test
```

Syntaxe, balisage des pages, classes orphelines, bonne formation des SVG et des
ressources Android, correspondance des jetons, cohérence du paquet, contraste,
refus des collecteurs, neuf suites de comportements et les essais de
l'application Laravel (`php artisan test`). Le balisage est vérifié sur les pages
telles que l'application les écrit. C'est ce qui doit passer avant chaque
publication.

```bash
npm run jetons     # régénère assets/jetons.json depuis tokens.css
npm run android    # régénère android/ depuis tokens.css
npm run robots     # régénère robots.txt, les fragments nginx et les .htaccess
```

`tokens.css` est la source faisant foi pour les deux premiers, la liste
d'agents de `outils/robots.js` pour le troisième : les fichiers produits ne se
modifient jamais à la main, et `npm test` refuse de les laisser diverger.

---

## Robots et fouille de données

Cinquante-quatre agents sont refusés en production : collecteurs
d'entraînement de modèles, assistants, aspirateurs de site et scanners de
vulnérabilité. Le refus tient en trois couches, et il vaut mieux savoir ce que
chacune arrête :

| Couche | Portée |
|---|---|
| `robots.txt` | Une demande. Respectée par les grandes maisons, lue par les autres pour savoir où regarder. |
| Refus par nom | Ce qui s'annonce. `GPTBot` reçoit `403` ; un agent qui se déclare Firefox passe. |
| Débit limité | Ce qui insiste. Protège le serveur, pas le contenu. nginx seulement. |
| `TDM-Reservation` | Rien techniquement : la réserve de fouille au sens du protocole TDM du W3C a une portée juridique. |

Les moteurs de recherche restent autorisés. Un système de conception que
personne ne trouve ne sert personne, et les refuser retirerait la documentation
des résultats sans gêner un seul collecteur d'entraînement. La page
Intégration détaille les deux réglages à faire avant la mise en production :
les adresses de supervision, et le sort des assistants déclenchés par une
personne.

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
