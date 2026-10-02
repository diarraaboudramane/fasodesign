#!/usr/bin/env bash
#
# Charte graphique de l'administration burkinabè
# Installation sur une machine Debian 12 ou Ubuntu 24.04
#
#   sudo env DEPOT_GIT=https://hote/organisation/charte.git \
#     COURRIEL=administration@domaine.gov.bf bash hebergement/installer.sh
#
# Met en place deux choses sur la même machine :
#
#   le site de la charte, servi en statique sur chartegraphique.gov.bf ;
#   le dépôt npm de l'administration, sur depot.chartegraphique.gov.bf,
#   qui sert aussi de miroir au registre public.
#
# Les nouvelles versions sont construites à part puis activées par un
# basculement de lien symbolique. Les versions précédentes restent
# disponibles. Le script ne touche pas au pare-feu ni au DNS.
set -euo pipefail

DOMAINE="${DOMAINE:-chartegraphique-21.mtdpce-test.gov.bf}"
DEPOT="${DEPOT:-depot.$DOMAINE}"
COURRIEL="${COURRIEL:-}"
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
[[ "$DEPOT" =~ ^([a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$ ]] ||
  { echo "DEPOT doit être un nom DNS valide." >&2; exit 1; }
[[ "$COURRIEL" =~ ^[^@[:space:]]+@[^@[:space:]]+\.[^@[:space:]]+$ ]] ||
  { echo "Renseigner COURRIEL avec une adresse valide pour activer HTTPS." >&2; exit 1; }
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
for commande in nginx git node npm certbot systemctl runuser useradd; do
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
    listen 80;
    listen [::]:80;
    server_name $DOMAINE www.$DOMAINE;
    root $CURRENT;
    include /etc/nginx/snippets/charte.conf;
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
# dépôt : une seule source, pas deux.
sed "s#^root .*#root $CURRENT;#" "$SOURCE/hebergement/nginx.conf" \
  > /etc/nginx/snippets/charte.conf

ln -sf /etc/nginx/sites-available/charte /etc/nginx/sites-enabled/charte

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

cat > /etc/systemd/system/verdaccio.service <<'UNIT'
[Unit]
Description=Depot npm de l'administration burkinabe
After=network.target

[Service]
Type=simple
User=verdaccio
ExecStart=/var/lib/verdaccio/npm/bin/verdaccio --config /etc/verdaccio/verdaccio.yaml
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
systemctl enable --now verdaccio

cat > /etc/nginx/sites-available/depot <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name $DEPOT;

    # Une archive npm depasse la limite par defaut de nginx.
    client_max_body_size 50m;

    # Le même refus que le site. Pas de limitation de débit : un
    # « npm install » envoie des dizaines de requêtes d'un coup.
    if (\$faso_collecteur) {
        return 403;
    }

    location / {
        proxy_pass http://127.0.0.1:4873/;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
NGINX
ln -sf /etc/nginx/sites-available/depot /etc/nginx/sites-enabled/depot

nginx -t
systemctl reload nginx

# ------------------------------------------------------------ certificats
dire "Certificats"
certbot --nginx --non-interactive --agree-tos --redirect \
  -m "$COURRIEL" -d "$DOMAINE" -d "www.$DOMAINE" -d "$DEPOT"

# ------------------------------------------------------------------ fin
dire "En place"
cat <<FIN

  Site      https://$DOMAINE
  Dépôt     https://$DEPOT

  Il reste deux gestes à faire à la main :

    1. créer le compte de publication du dépôt
         sudo -u verdaccio htpasswd -B /etc/verdaccio/htpasswd publication
         systemctl restart verdaccio

    2. vérifier que la machine joint le registre public, sans quoi le
       miroir ne se remplira pas
         curl -sS -o /dev/null -w '%{http_code}\\n' https://registry.npmjs.org/

  Pour mettre à jour le site après une nouvelle version :
    sudo bash $SOURCE/hebergement/installer.sh

FIN
