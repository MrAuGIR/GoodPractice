#!/usr/bin/env bash
#
# deploy/build.sh — Assemble le déploiement et l'envoie sur l'hébergement IONOS.
#
# Hébergement mutualisé : SSH OK, mais PAS de node/npm (→ build front en local),
# PAS de composer global (→ composer.phar + php8.2-cli sur le serveur).
# Le docroot du sous-domaine pointe sur un sous-dossier → layout Symfony STANDARD :
# le code vit dans ~/projets/GoodPractice/, le docroot est .../GoodPractice/public/
# (le code, parent du docroot, n'est donc pas accessible au web).
#
# Layout cible :
#   ~/projets/GoodPractice/        bin/ config/ migrations/ src/ templates/ data/
#                                  composer.* vendor/ var/ .env .env.local
#   ~/projets/GoodPractice/public/ ← docroot : index.php (Symfony) + .htaccess
#                                  + index.html (SPA) + assets/ (build Vite)
#
# Usage :
#   ./deploy/build.sh                 # build front + assemble build/site/
#   ./deploy/build.sh --no-front      # assemble sans rebuilder le front
#   SSH="<user>@<ssh-host>" ./deploy/build.sh --push
#   SSH="…" REMOTE_DIR="projets/GoodPractice" ./deploy/build.sh --push
#
# Le push (--push) : rsync --delete sur ~/projets/GoodPractice en PRÉSERVANT
#   vendor/, .env.local, config/jwt/, var/ et public/bundles/.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
STAGING="$ROOT/build/site"
SSH="${SSH:-}"
REMOTE_DIR="${REMOTE_DIR:-projets/GoodPractice}"
BUILD_FRONT=1
PUSH=0

for arg in "$@"; do
  case "$arg" in
    --no-front) BUILD_FRONT=0 ;;
    --push)     PUSH=1 ;;
    -h|--help)  grep '^#' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
    *) echo "Argument inconnu : $arg" >&2; exit 1 ;;
  esac
done

cd "$ROOT"

# 1. Build du front Vue (Vite lit .env.production → VITE_API_BASE=/index.php/api)
if [ "$BUILD_FRONT" -eq 1 ]; then
  echo "▶ Build front (frontend/)…"
  ( cd frontend && npm ci && npm run build )
fi
[ -d frontend/dist ] || { echo "✗ frontend/dist/ manquant — build le front d'abord." >&2; exit 1; }

# 2. Assemblage du staging = image exacte de ~/projets/GoodPractice
echo "▶ Assemblage de $STAGING …"
rm -rf "$STAGING"
mkdir -p "$STAGING/public"

# --- Code Symfony (hors docroot) ---
rsync -a backend/bin/        "$STAGING/bin/"
rsync -a --exclude='jwt/*.pem' backend/config/ "$STAGING/config/"
rsync -a backend/migrations/ "$STAGING/migrations/"
rsync -a backend/src/        "$STAGING/src/"
rsync -a backend/templates/  "$STAGING/templates/"
rsync -a backend/data/       "$STAGING/data/"        # JSON pour la commande d'import
cp backend/composer.json backend/composer.lock backend/symfony.lock "$STAGING/"
cp backend/.env          "$STAGING/.env"             # base env (committée) ; .env.local override serveur
cp deploy/.env.prod.dist "$STAGING/.env.local.dist"

# --- Docroot public/ : front-controller Symfony + SPA Vite ---
rsync -a backend/public/   "$STAGING/public/" --exclude='bundles/'   # index.php (.htaccess écrasé ensuite)
rsync -a frontend/dist/    "$STAGING/public/"                        # index.html + assets/ (+ favicon…)
cp deploy/htaccess-public  "$STAGING/public/.htaccess"

echo "✓ Staging prêt : $STAGING"

# 3. Transfert optionnel
if [ "$PUSH" -eq 1 ]; then
  [ -n "$SSH" ] || { echo "✗ Variable SSH non définie (ex: SSH=\"<user>@<ssh-host>\")" >&2; exit 1; }

  echo "▶ Préparation du dossier distant ~/$REMOTE_DIR …"
  ssh "$SSH" "mkdir -p ~/$REMOTE_DIR/public"

  echo "▶ Envoi (--delete, en préservant vendor/ .env.local config/jwt/ var/ public/bundles/) …"
  rsync -avz --delete \
    --exclude='/vendor/' --exclude='/.env.local' --exclude='/config/jwt/' \
    --exclude='/var/' --exclude='/public/bundles/' \
    "$STAGING/" "$SSH":"$REMOTE_DIR/"

  cat <<EOF

✓ Transfert terminé (cible : ~/$REMOTE_DIR).
  → Pointer le docroot du sous-domaine sur : ~/$REMOTE_DIR/public

Étapes serveur (SSH) — au PREMIER déploiement :
  cd ~/$REMOTE_DIR
  cp .env.local.dist .env.local && nano .env.local      # DATABASE_URL, APP_SECRET, JWT_PASSPHRASE
  [ -f composer.phar ] || (php8.2-cli -r "copy('https://getcomposer.org/installer','cs.php');" && php8.2-cli cs.php && rm -f cs.php)
  php8.2-cli composer.phar install --no-dev --optimize-autoloader --classmap-authoritative
  php8.2-cli bin/console lexik:jwt:generate-keypair --skip-if-exists
  php8.2-cli bin/console doctrine:migrations:migrate -n
  php8.2-cli bin/console app:import:bonnes-pratiques data/bonnes-pratiques.json
  php8.2-cli bin/console app:user:create admin@goodpractice.fr --role=ROLE_ADMIN
  php8.2-cli bin/console cache:clear

Mises à jour : relancer le script, puis sur le serveur :
  php8.2-cli composer.phar install --no-dev --optimize-autoloader
  php8.2-cli bin/console doctrine:migrations:migrate -n && php8.2-cli bin/console cache:clear
EOF
else
  cat <<EOF

Staging assemblé dans build/site/. Pour transférer :
  SSH="<user>@<ssh-host>" ./deploy/build.sh --push
EOF
fi
