#!/usr/bin/env bash
#
# Charte graphique de l'administration burkinabè
# Installation sur une machine Debian 12 ou Ubuntu 24.04
#
#   sudo bash hebergement/installer.sh
#
# Met en place deux choses sur la même machine :
#
#   le site de la charte, servi en statique sur chartegraphique.gov.bf ;
#   le dépôt npm de l'administration, sur depot.chartegraphique.gov.bf,
#   qui sert aussi de miroir au registre public.
#
# Le script est idempotent : le relancer ne casse rien et reprend là
# où il en était. Il ne touche pas au pare-feu ni au DNS, qui
# relèvent de l'administration du réseau.
set -euo pipefail

DOMAINE="${DOMAINE:-chartegraphique.gov.bf}"
DEPOT="${DEPOT:-depot.$DOMAINE}"
COURRIEL="${COURRIEL:-}"            # pour les certificats
RACINE="/var/www/charte"
SOURCE="${SOURCE:-/opt/charte-graphique}"

dire() { printf '\n\033[1m== %s\033[0m\n' "$1"; }

[ "$(id -u)" -eq 0 ] || { echo "À lancer avec sudo." >&2; exit 1; }

# --------------------------------------------------------------- paquets
dire "Paquets"
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y --no-install-recommends \
  nginx git curl ca-certificates certbot python3-certbot-nginx

# Node 20 : la version des dépôts est souvent trop ancienne pour
# Verdaccio. On prend celle de NodeSource, épinglée.
if ! command -v node >/dev/null || [ "$(node -p 'process.versions.node.split(".")[0]')" -lt 20 ]; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt-get install -y nodejs
fi
node --version

# ------------------------------------------------------------ le système
dire "Récupération de la charte"
if [ -d "$SOURCE/.git" ]; then
  git -C "$SOURCE" pull --ff-only
else
  git clone "${DEPOT_GIT:?Renseigner DEPOT_GIT=https://…/charte-graphique.git}" "$SOURCE"
fi

dire "Vérification avant mise en ligne"
# La même recette qu'en local. Si elle échoue, rien n'est publié.
( cd "$SOURCE" && npm test )

dire "Construction du site"
install -d "$RACINE"
( cd "$SOURCE" && FASO_SITE="$RACINE" node outils/site.js )
chown -R www-data:www-data "$RACINE"

# ----------------------------------------------------------------- nginx
dire "Service web"
install -d /etc/nginx/sites-available /etc/nginx/sites-enabled

cat > /etc/nginx/sites-available/charte <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAINE www.$DOMAINE;
    root $RACINE;
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
sed "s#^root .*#root $RACINE;#" "$SOURCE/hebergement/nginx.conf" \
  > /etc/nginx/snippets/charte.conf

ln -sf /etc/nginx/sites-available/charte /etc/nginx/sites-enabled/charte
rm -f /etc/nginx/sites-enabled/default

# --------------------------------------------------------------- dépôt npm
dire "Dépôt npm"
if ! id verdaccio >/dev/null 2>&1; then
  useradd --system --create-home --home-dir /var/lib/verdaccio verdaccio
fi
npm install -g verdaccio

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
ExecStart=/usr/bin/verdaccio --config /etc/verdaccio/verdaccio.yaml
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
if [ -n "$COURRIEL" ]; then
  certbot --nginx --non-interactive --agree-tos --redirect \
    -m "$COURRIEL" -d "$DOMAINE" -d "www.$DOMAINE" -d "$DEPOT"
else
  echo "  COURRIEL non renseigné : certificats à poser ensuite par"
  echo "    certbot --nginx -d $DOMAINE -d www.$DOMAINE -d $DEPOT"
fi

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
