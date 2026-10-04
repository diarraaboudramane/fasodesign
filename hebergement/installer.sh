#!/usr/bin/env bash
#
# Charte graphique de l'administration burkinabè
# Installation sur une machine Debian 12 ou Ubuntu 24.04
#
#   Derrière un proxy qui termine le TLS (cas par défaut) :
#   sudo env DEPOT_GIT=https://hote/organisation/charte.git \
#     PROXY_AMONT=10.0.0.1 bash hebergement/installer.sh
#
#   Machine exposée directement, certificat posé ici :
#   sudo env DEPOT_GIT=https://hote/organisation/charte.git TLS=local \
#     COURRIEL=administration@domaine.gov.bf bash hebergement/installer.sh
#
# Met en place deux choses sur la même machine :
#
#   le site de la charte, servi en statique sur $DOMAINE ;
#   le dépôt npm de l'administration, sous $DOMAINE/npm/, qui sert
#   aussi de miroir au registre public. Il partage le nom et le
#   certificat du site : aucun sous-domaine à déclarer.
#
# Les nouvelles versions sont construites à part puis activées par un
# basculement de lien symbolique. Les versions précédentes restent
# disponibles. Le script ne touche pas au pare-feu ni au DNS.
set -euo pipefail

DOMAINE="${DOMAINE:-chartegraphique-21.mtdpce-test.gov.bf}"
COURRIEL="${COURRIEL:-}"
# « amont » : un proxy termine le TLS et parle HTTP à cette machine.
# « local » : cette machine reçoit Internet et obtient son certificat.
TLS="${TLS:-amont}"
# Adresses du proxy amont (IP ou CIDR, séparées par des espaces).
PROXY_AMONT="${PROXY_AMONT:-}"
RACINE="/var/www/charte"
SOURCE="${SOURCE:-/opt/charte-graphique}"
DEPOT_GIT="${DEPOT_GIT:-}"
VERDACCIO_VERSION="${VERDACCIO_VERSION:-6.10.4}"
BUILD_USER="charte-build"
RELEASES="$RACINE/releases"
CURRENT="$RACINE/current"
STAGING=""

dire() { printf '\n\033[1m== %s\033[0m\n' "$1"; }

nettoyer() {
  if [ -n "$STAGING" ] && [[ "$STAGING" == "$RELEASES"/.build-* ]]; then
    rm -rf -- "$STAGING"
  fi
}
trap nettoyer EXIT

[ "$(id -u)" -eq 0 ] || { echo "À lancer avec sudo." >&2; exit 1; }

[[ "$DOMAINE" =~ ^([a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$ ]] ||
  { echo "DOMAINE doit être un nom DNS valide." >&2; exit 1; }
case "$TLS" in
  local)
    [[ "$COURRIEL" =~ ^[^@[:space:]]+@[^@[:space:]]+\.[^@[:space:]]+$ ]] ||
      { echo "Renseigner COURRIEL avec une adresse valide pour activer HTTPS." >&2; exit 1; }
    ;;
  amont)
    # Sans l'adresse du proxy, tous les visiteurs ont la sienne : la
    # limitation de débit des pages les compterait comme un seul.
    [ -n "$PROXY_AMONT" ] ||
      { echo "Renseigner PROXY_AMONT avec l'adresse du proxy qui termine le TLS." >&2; exit 1; }
    for adresse in $PROXY_AMONT; do
      [[ "$adresse" =~ ^[0-9a-fA-F.:]+(/[0-9]{1,3})?$ ]] ||
        { echo "PROXY_AMONT : adresse invalide : $adresse" >&2; exit 1; }
    done
    ;;
  *) echo "TLS vaut « amont » ou « local »." >&2; exit 1 ;;
esac
[ -n "$DEPOT_GIT" ] ||
  { echo "Renseigner DEPOT_GIT avec l'URL du dépôt." >&2; exit 1; }
[[ "$VERDACCIO_VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] ||
  { echo "VERDACCIO_VERSION doit être une version exacte, par exemple 6.10.4." >&2; exit 1; }
[ -d "$SOURCE/.git" ] || [ ! -e "$SOURCE" ] ||
  { echo "SOURCE existe mais ne contient pas un dépôt Git : $SOURCE" >&2; exit 1; }
if [ -e "$CURRENT" ] && [ ! -L "$CURRENT" ]; then
  echo "$CURRENT existe et n'est pas un lien symbolique ; migration manuelle requise." >&2
  exit 1
fi

# ------------------------------------------------------ prérequis locaux
dire "Vérification des logiciels déjà installés"
commandes="nginx git node npm curl systemctl runuser useradd"
[ "$TLS" = local ] && commandes="$commandes certbot"
for commande in $commandes; do
  command -v "$commande" >/dev/null 2>&1 || {
    echo "Commande requise absente : $commande. Installez-la avant de continuer." >&2
    exit 1
  }
done

# Cette procédure n'installe ni ne met à jour les paquets système.
# Verdaccio 6 demande Node.js 22 ou plus récent.
NODE_MAJOR="$(node -p 'process.versions.node.split(".")[0]')"
[ "$NODE_MAJOR" -ge 22 ] || {
  echo "Node.js 22 ou plus récent est requis pour Verdaccio 6 (version trouvée : $(node --version))." >&2
  exit 1
}
node --version

# ------------------------------------------------------------ le système
dire "Récupération de la charte"
if [ -d "$SOURCE/.git" ]; then
  ORIGINE="$(git -C "$SOURCE" remote get-url origin)"
  [ "$ORIGINE" = "$DEPOT_GIT" ] || {
    echo "L'origine Git de $SOURCE ne correspond pas à DEPOT_GIT." >&2
    exit 1
  }
  git -C "$SOURCE" pull --ff-only
else
  git clone "$DEPOT_GIT" "$SOURCE"
fi

if ! id "$BUILD_USER" >/dev/null 2>&1; then
  useradd --system --create-home --home-dir "/var/lib/$BUILD_USER" \
    --shell /usr/sbin/nologin "$BUILD_USER"
fi

dire "Construction isolée et vérification avant mise en ligne"
install -d -o root -g www-data -m 0755 "$RACINE"
install -d -o root -g www-data -m 0755 "$RELEASES"
STAGING="$RELEASES/.build-$$"
install -d -o "$BUILD_USER" -g "$BUILD_USER" -m 0755 "$STAGING"
runuser -u "$BUILD_USER" -- env -i \
  PATH=/usr/bin:/bin HOME="/var/lib/$BUILD_USER" FASO_SITE="$STAGING" \
  bash -c 'cd "$1" && npm test && node outils/site.js' _ "$SOURCE"

# Conserver les ressources déjà publiées : leurs URL versionnées sont
# immuables et peuvent encore être utilisées par des services tiers.
if [ -L "$CURRENT" ]; then
  SITE_PRECEDENT="$(readlink -f "$CURRENT")"
else
  SITE_PRECEDENT="$RACINE"
fi
shopt -s nullglob
for ancien in "$SITE_PRECEDENT"/[0-9]*.[0-9]*.[0-9]*; do
  [ -d "$ancien" ] || continue
  nom_ancien="${ancien##*/}"
  if [[ "$nom_ancien" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] &&
     [ ! -e "$STAGING/$nom_ancien" ]; then
    cp -a -- "$ancien" "$STAGING/$nom_ancien"
  fi
done
shopt -u nullglob

RELEASE="$RELEASES/$(date -u +%Y%m%dT%H%M%SZ)-$$"
mv -- "$STAGING" "$RELEASE"
STAGING=""
chown -R root:www-data "$RELEASE"
chmod -R u=rwX,g=rX,o=rX "$RELEASE"
LIEN_TEMPORAIRE="$RACINE/.current-$$"
ln -s "releases/${RELEASE##*/}" "$LIEN_TEMPORAIRE"
mv -Tf -- "$LIEN_TEMPORAIRE" "$CURRENT"

# ----------------------------------------------------------------- nginx
dire "Service web"
install -d /etc/nginx/sites-available /etc/nginx/sites-enabled

cat > /etc/nginx/sites-available/charte <<NGINX
server {
    # Serveur par défaut : un proxy amont peut transmettre un autre nom
    # d'hôte que le domaine, et la machine ne sert que la charte.
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name $DOMAINE www.$DOMAINE;
    root $CURRENT;
    include /etc/nginx/snippets/charte.conf;

    # Le dépôt npm, sous le même nom. « ^~ » l'emporte sur les
    # emplacements par expression du fragment, et la limitation de
    # débit des pages ne s'y applique pas : un « npm install » envoie
    # des dizaines de requêtes d'un coup. Le refus des collecteurs, posé
    # au niveau du bloc server par le fragment, s'y applique.
    location = /npm {
        return 301 /npm/;
    }
    location ^~ /npm/ {
        # Une archive npm dépasse la limite par défaut de nginx.
        client_max_body_size 50m;
        proxy_pass http://127.0.0.1:4873/;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;

        # add_header remplace le jeu hérité : sans CSP ici, l'interface
        # de Verdaccio, qui emploie du script en ligne, reste utilisable.
        add_header X-Content-Type-Options "nosniff" always;
        add_header Referrer-Policy "strict-origin-when-cross-origin" always;
        add_header X-Frame-Options "DENY" always;
        add_header X-Robots-Tag "noindex, noai, noimageai" always;
    }
}
NGINX

# Le refus des collecteurs et les zones de débit vivent dans le
# contexte « http », que Debian et Ubuntu lisent dans conf.d. Sans ce
# fichier, le fragment ci-dessous nomme des variables et des zones qui
# n'existent pas, et nginx refuse de démarrer.
install -m 644 "$SOURCE/hebergement/faso-robots.conf" /etc/nginx/conf.d/faso-robots.conf
grep -q 'conf\.d/\*\.conf' /etc/nginx/nginx.conf ||
  { echo "nginx.conf n'inclut pas conf.d/*.conf : y ajouter, dans le bloc http,
    include /etc/nginx/conf.d/faso-robots.conf;" >&2; exit 1; }

install -d /etc/nginx/snippets
# Les règles de cache, de compression et d'en-têtes sont celles du
# dépôt : une seule source, pas deux. La racine est posée par le bloc
# server ci-dessus : la laisser aussi dans le fragment la déclarerait
# deux fois, et nginx refuse de démarrer.
sed '/^[[:space:]]*root[[:space:]]/d' "$SOURCE/hebergement/nginx.conf" \
  > /etc/nginx/snippets/charte.conf

# Le site d'accueil de la distribution revendique lui aussi le rôle de
# serveur par défaut : deux à la fois, nginx refuse de démarrer.
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/charte /etc/nginx/sites-enabled/charte

# Derrière un proxy, l'adresse du visiteur est dans X-Forwarded-For.
# Elle n'est crue que si la requête vient du proxy déclaré.
if [ "$TLS" = amont ]; then
  {
    echo "# Engendré par hebergement/installer.sh : proxy amont qui termine le TLS."
    for adresse in $PROXY_AMONT; do echo "set_real_ip_from $adresse;"; done
    echo "real_ip_header X-Forwarded-For;"
    echo "real_ip_recursive on;"
  } > /etc/nginx/conf.d/faso-proxy.conf
else
  rm -f /etc/nginx/conf.d/faso-proxy.conf
fi

# --------------------------------------------------------------- dépôt npm
dire "Dépôt npm"
if ! id verdaccio >/dev/null 2>&1; then
  useradd --system --create-home --home-dir /var/lib/verdaccio verdaccio
fi
install -d -o verdaccio -g verdaccio /var/lib/verdaccio/npm
# Installer les dépendances du dépôt npm sous son compte de service,
# jamais avec les privilèges root.
runuser -u verdaccio -- env -i \
  PATH=/usr/bin:/bin HOME=/var/lib/verdaccio \
  npm install --prefix /var/lib/verdaccio/npm "verdaccio@$VERDACCIO_VERSION"

install -d /etc/verdaccio
[ -f /etc/verdaccio/verdaccio.yaml ] ||
  install -m 644 "$SOURCE/hebergement/verdaccio.yaml" /etc/verdaccio/verdaccio.yaml
# Aucune inscription libre : le premier compte se crée à la main.
[ -f /etc/verdaccio/htpasswd ] || : > /etc/verdaccio/htpasswd
chown -R verdaccio:verdaccio /etc/verdaccio /var/lib/verdaccio

cat > /etc/systemd/system/verdaccio.service <<UNIT
[Unit]
Description=Depot npm de l'administration burkinabe
After=network.target

[Service]
Type=simple
User=verdaccio
# Adresse publique : les liens vers les archives restent en https même
# si le TLS est terminé en amont de cette machine.
Environment=VERDACCIO_PUBLIC_URL=https://$DOMAINE
# Installation locale (--prefix) : l'exécutable est sous node_modules/.bin.
ExecStart=/var/lib/verdaccio/npm/node_modules/.bin/verdaccio --config /etc/verdaccio/verdaccio.yaml
Restart=on-failure
RestartSec=5
# Le depot n'a besoin d'ecrire que dans son propre stockage.
ProtectSystem=strict
ProtectHome=true
PrivateTmp=true
NoNewPrivileges=true
ReadWritePaths=/var/lib/verdaccio

[Install]
WantedBy=multi-user.target
UNIT

systemctl daemon-reload
systemctl enable verdaccio
systemctl restart verdaccio
# Un service qui ne démarre pas ne se voit qu'en 502 côté nginx :
# on le constate ici, avec son journal, plutôt qu'en production.
for _ in $(seq 1 30); do
  curl -fsS -o /dev/null http://127.0.0.1:4873/-/ping && break
  sleep 1
done
curl -fsS -o /dev/null http://127.0.0.1:4873/-/ping || {
  echo "Verdaccio ne répond pas sur 127.0.0.1:4873." >&2
  journalctl -u verdaccio -n 30 --no-pager >&2
  exit 1
}

# Ancienne configuration sur sous-domaine, remplacée par /npm/.
rm -f /etc/nginx/sites-enabled/depot /etc/nginx/sites-available/depot

# Une autre configuration qui revendique le même nom l'emporte sur
# celle-ci, et le site resterait invisible sans aucune erreur.
VERIF_NGINX="$(nginx -t 2>&1)" || { echo "$VERIF_NGINX" >&2; exit 1; }
echo "$VERIF_NGINX"
if grep -q 'conflicting server name' <<<"$VERIF_NGINX"; then
  echo "Une autre configuration nginx déclare déjà $DOMAINE :" >&2
  nginx -T 2>/dev/null | grep -nE '^# configuration file|server_name' >&2
  echo "La désactiver, puis relancer ce script." >&2
  exit 1
fi
systemctl reload nginx

# ------------------------------------------------------------ certificats
dire "Certificats"
if [ "$TLS" = local ]; then
  certbot --nginx --non-interactive --agree-tos --redirect \
    -m "$COURRIEL" -d "$DOMAINE" -d "www.$DOMAINE"
else
  echo "TLS terminé par le proxy amont ($PROXY_AMONT) : rien à faire ici."
fi

# ------------------------------------------------------------------ fin
dire "En place"
cat <<FIN

  Site      https://$DOMAINE
  Dépôt     https://$DOMAINE/npm/

  Il reste deux gestes à faire à la main :

    1. créer le compte de publication du dépôt
         sudo -u verdaccio htpasswd -B /etc/verdaccio/htpasswd publication
         systemctl restart verdaccio

    2. vérifier que la machine joint le registre public, sans quoi le
       miroir ne se remplira pas
         curl -sS -o /dev/null -w '%{http_code}\\n' https://registry.npmjs.org/

  Pour mettre à jour le site après une nouvelle version, relancer
  ce script avec les mêmes variables.

FIN
