# Charte graphique de l'administration burkinabè

Système de conception des services numériques de l'État burkinabè : jetons,
composants CSS, comportements JavaScript, gabarits institutionnels.

Aucune dépendance, aucune étape de compilation. Les fichiers se servent tels
quels ou s'installent comme n'importe quel paquet npm.

La charte applique le « Guide pour la définition de la charte graphique des
plateformes numériques de l'administration publique » (MTDPCE/SG/DGTD,
10/08/2024) : couleurs officielles du drapeau, Montserrat pour les titres et
les boutons d'action, Roboto pour le texte, palettes réservées aux espaces
public et privé, structure des pages. Ce qu'elle n'en applique pas tel quel
est motivé dans la page Gouvernance de la documentation, section « Écarts
assumés avec le guide ».

---

## Installation

Pour un projet qui démarre, une commande écrit les fichiers de départ et
installe la charte :

```bash
npx https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-create-charte-graphique-3.0.0.tgz
```

Elle demande le dossier, le type de projet — `statique`, `react`, `angular` ou
`laravel` — et le dépôt npm interne si le service en emploie un. Tout peut se
donner sur la ligne de commande :

```bash
npx --yes https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-create-charte-graphique-3.0.0.tgz \
  mon-service --type=react --oui
```

La charte porte les armoiries, le drapeau et la devise de l'État : la commande
rappelle les conditions d'emploi et demande de les accepter avant d'installer
quoi que ce soit. Sur une chaîne d'intégration, `CHARTE_ACCEPTE_LICENCE=oui`.

Pour un projet déjà commencé :

```bash
npm install https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-3.0.0.tgz
```

L'espace de noms `@govbf` n'est encore sur aucun registre : les deux archives
sont servies par l'application elle-même (`/npm/…`, construites depuis le dépôt).
Le paquet installé s'appelle `@govbf/charte-graphique`, et c'est sous ce nom qu'il
s'importe. Les dépôts possibles pour la suite sont comparés dans la page
Intégration, section « Publier et servir en ligne ».

Puis, dans le projet :

```js
import '@govbf/charte-graphique/css/tokens.css';
import '@govbf/charte-graphique/css/faso.css';
import '@govbf/charte-graphique/css/icones.css';
import Faso from '@govbf/charte-graphique';

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
| `@govbf/charte-graphique` | Comportements, avec leurs déclarations TypeScript |
| `…/css/tokens.css` | 202 jetons, thèmes clair et sombre, déclarations de polices |
| `…/css/faso.css` | 66 composants |
| `…/css/icones.css` | 35 icônes, appliquées en masque CSS |
| `…/amorce` | Pose le thème retenu avant le premier rendu |
| `…/jetons.json` | Les jetons, valeurs résolues, pour les outils de conception |
| `…/img/armoiries.svg` | Grand format, à partir de 64 px |
| `…/img/armoiries-moyen.svg` | Moyen format, 32 à 64 px, un tiers plus léger |
| `…/img/armoiries-ecu.svg` | Petit format, 16 à 32 px : l'écu seul |
| `…/img/armoiries-gris.svg` | Impression en une seule encre, photocopie |
| `…/img/armoiries-512.png` … `-64.png` | Bureautique, courriel, icône d'application |
| `…/img/favicon.svg` | Mini format, 16 px et en dessous : le drapeau et son étoile |
| `…/polices/*` | Montserrat et Roboto, variables, sous-ensemble latin, licences comprises (`montserrat.woff2`, `roboto.woff2`, `montserrat-OFL.txt`, `roboto-OFL.txt`) |
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

Le niveau visé est WCAG 2.1 AA. Les 130 couples de couleurs effectivement
employés par la feuille de styles passent leur seuil dans les deux thèmes, et
c'est vérifié par `npm test`.

Ce qui n'a pas été fait est écrit aussi : aucun essai avec un lecteur d'écran
réel, aucun audit par un tiers, aucun test avec des personnes en situation de
handicap. La formule juste est donc « conçu pour WCAG 2.1 AA, vérifié par la
mesure sur les points listés », et non « conforme ».

---

## La documentation : une application Laravel

Le paquet npm ne change pas : il ne contient ni PHP ni Laravel. C'est le site
de documentation qui est une application Laravel 13 (PHP 8.3 ou plus). La
documentation se lit sans base de données. Seul l'outil de contrôle de
conformité en a une, une base SQLite pour ses comptes et ses bilans. Une case
reCAPTCHA peut être demandée avant toute page (voir plus bas ; elle est coupée
pour l'instant sur le serveur de test). Les pages de documentation ne posent
aucun autre cookie que celui qui retient cette vérification. Seules `/contact`,
`/verification` et `/conformite/…` ouvrent une session.

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
| `app/Http/Controllers/FichierController.php` | Sert `assets/`, `archives/`, `android/` et la diffusion figée `/3.0.0/…` depuis le dépôt, sans copie dans `public/` |
| `app/Http/Middleware/EnTetesDeSecurite.php` | Politique de sécurité, réserve de fouille, cache |
| `app/Support/Audience.php` | Personnes en ligne (actives depuis 5 minutes) et visiteurs du mois, au pied de l'accueil. Seuls les visiteurs vérifiés comptent, ou, case coupée, les navigateurs qui ne se déclarent pas automates ; aucune écriture sur l'appareil, aucune adresse IP gardée, une empreinte hachée qui change chaque mois. `CHARTE_AUDIENCE=false` la coupe |
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

- les fichiers de la charte (`/assets/…`, `/3.0.0/…`), que les services des
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

### Contrôle de conformité

`/conformite` vérifie qu'un site ou un projet respecte la charte et en rend un
bilan. On soumet l'adresse d'un site en ligne, ou l'archive ZIP de son code. Il
faut un compte : une adresse en `.gov.bf` est ouverte dès qu'elle est confirmée,
les autres attendent qu'un administrateur les valide.

| Chemin | Rôle |
| --- | --- |
| `app/Conformite/Regles/` | Les 17 règles, une classe chacune, avec sa gravité et la section de la charte qui l'explique |
| `app/Conformite/Moteur.php` | Applique les règles et calcule le niveau : conforme, conforme avec réserves, non conforme |
| `app/Conformite/Collecte/` | Le parcours d'un site (`CollecteAdresse`), la lecture d'une archive (`CollecteArchive`), la garde réseau |
| `app/Conformite/LecteurZip.php` | Lecture des archives en mémoire, sans l'extension zip ni rien écrire sur le disque |
| `app/Conformite/Reference.php` | Classes, empreintes et version de la charte, lues dans le dépôt lui-même |
| `app/Jobs/AnalyserProjet.php` | Une analyse en arrière-plan ; l'archive est supprimée à la fin |

Ce qui protège le serveur :

- **Adresses** : seuls `http` et `https` sur les ports 80 et 443 sont admis. Les
  adresses du réseau interne sont refusées, y compris derrière une redirection,
  et l'appel se fait sur l'adresse IP vérifiée. On ne suit que trois
  redirections, on lit au plus 2 Mo par réponse et 20 pages, en 90 secondes.
- **Archives** : 50 Mo au plus. L'archive est refusée si elle contient un chemin
  qui sort du dossier, un lien symbolique, une entrée chiffrée ou ZIP64, plus de
  5 000 fichiers, plus de 200 Mo une fois décompressée, ou un taux de
  compression de bombe. Rien n'est exécuté ni installé.
- **Comptes** : 20 analyses par jour et par compte. Les tentatives de connexion
  sont limitées, et les bilans sont supprimés au bout d'un an.

Mise en service :

```bash
touch database/database.sqlite
php artisan migrate --force
php artisan charte:courriel-essai prenom.nom@mtdpce.gov.bf   # l'envoi des courriels doit marcher : sans lui, aucun compte ne se confirme
php artisan charte:administrateur prenom.nom@mtdpce.gov.bf   # après avoir créé ce compte sur /conformite/inscription
php artisan charte:confirmer-compte prenom.nom@tic.gov.bf    # dépannage : confirme une adresse dont le lien n'est pas arrivé
php artisan queue:work --tries=1 --timeout=300               # à garder lancé (systemd, supervisor)
# cron, chaque minute : php artisan schedule:run   (purge des bilans de plus d'un an)
```

Sur un poste de développement, `CONFORMITE_RESEAU_LOCAL=true` permet d'analyser
`localhost`. Le réglage est sans effet en production.

**Rendu JavaScript.** C'est une option de l'analyse par adresse, pour les
applications React, Angular ou Vue rendues dans le navigateur. Un Chrome sans
interface ouvre chaque page et exécute son code. L'analyse porte alors sur la
page affichée, et mesure en plus les contrastes réels et le poids réel, images
et polices comprises. Le travail est fait par `outils/rendu/rendu.js`.

Le code exécuté est celui d'un tiers. Tout ce que Chrome demande au réseau
passe donc par un mandataire ouvert par le script, qui applique la même garde
que l'analyse simple : pas de réseau interne, `http` et `https` seulement, sur
les ports 80 et 443. Les WebSocket passent aussi par ce mandataire, et WebRTC
est coupé. `node tests/rendu.js` le vérifie avec un vrai Chrome : une page qui
tente de joindre un serveur interne ne l'atteint pas.

```bash
cd outils/rendu && npm install --omit=dev          # puppeteer-core, hors du paquet de la charte
apt install chromium                               # ou Google Chrome
# .env : CONFORMITE_CHROME=/usr/bin/chromium   (et CONFORMITE_NODE si node n'est pas dans le PATH)
```

Lancer la file d'attente sous un compte ordinaire, pas root : sous root, Chrome
tourne sans son bac à sable. Pour une isolation complète, faire tourner ce compte
dans un conteneur sans accès au réseau interne. Sans `CONFORMITE_CHROME`,
l'option reste grisée dans le formulaire.

Ce que l'outil ne vérifie pas est écrit dans chaque bilan. Il ne juge ni la
clarté des textes ni l'usage réel au clavier, et il ne voit pas le contenu
ajouté par JavaScript. Le bilan ne vaut ni audit d'accessibilité ni
homologation.

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

Les commandes d'installation de la page Intégration (`npm install …/npm/…tgz`,
`npx …`, adresses figées `/3.0.0/…`) désignent toutes le serveur de test, quel que
soit le domaine qui sert la page. À la bascule, définir
`CHARTE_SERVEUR=https://chartegraphique.gov.bf` et changer la constante `SERVEUR`
d'`amorce/index.js`.

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

Cinquante-deux agents sont refusés en production : collecteurs
d'entraînement de modèles, aspirateurs de site, scanners de vulnérabilité, et
les assistants autres que ChatGPT et Claude, qui n'ont accès qu'à `/charte.md`.
ChatGPT-User et Claude-User, qui lisent à la demande d'une personne, ont accès
à tout le site ; GPTBot et ClaudeBot, qui collectent pour l'entraînement,
restent refusés. Le refus tient en plusieurs couches, et il vaut mieux savoir
ce que chacune arrête :

| Couche | Portée |
|---|---|
| `robots.txt` | Une demande. Respectée par les grandes maisons, lue par les autres pour savoir où regarder. |
| Refus par nom | Ce qui s'annonce. `GPTBot` reçoit `403` ; un agent qui se déclare Firefox passe. |
| Débit limité | Ce qui insiste. Protège le serveur, pas le contenu. nginx seulement. |
| `TDM-Reservation` | Rien techniquement : la réserve de fouille au sens du protocole TDM du W3C a une portée juridique. |

Les moteurs de recherche restent autorisés. Un système de conception que
personne ne trouve ne sert personne, et les refuser retirerait la documentation
des résultats sans gêner un seul collecteur d'entraînement. Reste un réglage
à faire avant la mise en production, détaillé dans la page Intégration : les
adresses de supervision.

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

Les polices Montserrat (© The Montserrat Project Authors) et Roboto
(© The Roboto Project Authors) sont distribuées sous licence SIL Open Font
License 1.1, dont le texte accompagne les fichiers (`montserrat-OFL.txt`,
`roboto-OFL.txt`).
