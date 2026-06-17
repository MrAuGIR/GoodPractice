# Déploiement — goodPractice (Symfony + Vue) sur IONOS mutualisé

Cible : hébergement mutualisé **IONOS** (`webspace-data.io`) avec **SSH**, mais
**sans node/npm** (build front en local) et **sans composer global** (`composer.phar`
+ `php8.2-cli` sur le serveur).

Contrairement à Portfolio (docroot figé sur le home → hack « à plat »), ici le **docroot
du sous-domaine peut pointer sur un sous-dossier**. On utilise donc le **layout Symfony
standard** : le code vit dans `~/projets/GoodPractice/`, et le docroot pointe sur
`~/projets/GoodPractice/public/`. Le code (parent du docroot) n'est ainsi **pas
accessible au web** — pas besoin de protection `.htaccess` sur le code privé.

- **Sous-domaine** : `goodpractice.aureliengirard.fr` → docroot `~/projets/GoodPractice/public/`.
- Déploiement **en place** : remplace l'ancien site PHP (2021). Sauvegarde avant (§ Rollback).

> Remplace `<user>@<ssh-host>` par les identifiants SSH/FTP de l'hébergeur.

## Architecture sur le serveur

```
~/projets/GoodPractice/          (hors docroot → non servi)
├── bin/ config/ migrations/ src/ templates/ data/
├── composer.json composer.lock symfony.lock
├── vendor/                       (installé sur le serveur)
├── config/jwt/*.pem              (générées sur le serveur)
├── var/                          (cache/logs, créés sur le serveur)
├── .env                          base (committée)
├── .env.local                    secrets prod (NON transféré, créé sur le serveur)
└── public/                       ← docroot du sous-domaine
    ├── index.php                 front-controller Symfony (standard, inchangé)
    ├── .htaccess                 ← deploy/htaccess-public
    ├── index.html                SPA Vue (DirectoryIndex)
    ├── assets/ favicon …         statiques réels (build Vite)
    └── bundles/                  (assets:install, généré sur le serveur)
```

Routage (sans dépendre de `mod_rewrite`) :
- **SPA** servie par `DirectoryIndex index.html` ; deep-links Vue via `ErrorDocument 404`.
- **API** appelée en `https://goodpractice.aureliengirard.fr/index.php/api/...` (PATH_INFO),
  cf. `frontend/.env.production` → `VITE_API_BASE`. Les IRIs renvoyés par l'API restent
  en `/api/...` (identifiants logiques, `iri()` dans `http.ts`).
- **Authorization (Bearer JWT)** propagé à PHP via `SetEnvIf` (Apache ne le passe pas
  par défaut sans mod_php).

## Procédure

### 1. Sauvegarde de l'existant (1er déploiement seulement)
```bash
SSH="<user>@<ssh-host>"
ssh "$SSH" 'tar czf ~/archives/goodpractice-legacy-$(date +%F).tgz -C ~/projets GoodPractice'
```

### 2. Build + transfert (script `deploy/build.sh`)
Build le front en local (Vite lit `frontend/.env.production`), assemble `build/site/`
à l'image du serveur, puis pousse en rsync :
```bash
SSH="<user>@<ssh-host>" ./deploy/build.sh --push
# REMOTE_DIR=projets/GoodPractice par défaut
```
`rsync --delete` en préservant `vendor/`, `.env.local`, `config/jwt/`, `var/`, `public/bundles/`.

### 3. Pointer le docroot
Dans le panneau IONOS, faire pointer le sous-domaine `goodpractice.aureliengirard.fr`
sur **`~/projets/GoodPractice/public/`** (une seule fois).

### 4. Premier déploiement — étapes serveur (SSH)
```bash
cd ~/projets/GoodPractice
cp .env.local.dist .env.local && nano .env.local     # DATABASE_URL, APP_SECRET, JWT_PASSPHRASE
[ -f composer.phar ] || (php8.2-cli -r "copy('https://getcomposer.org/installer','cs.php');" && php8.2-cli cs.php && rm -f cs.php)
php8.2-cli composer.phar install --no-dev --optimize-autoloader --classmap-authoritative
php8.2-cli bin/console lexik:jwt:generate-keypair --skip-if-exists
php8.2-cli bin/console doctrine:migrations:migrate -n
php8.2-cli bin/console app:import:bonnes-pratiques data/bonnes-pratiques.json
php8.2-cli bin/console app:user:create admin@goodpractice.fr --role=ROLE_ADMIN
php8.2-cli bin/console cache:clear
```
> La base MySQL (vide) est fournie par l'hébergeur. MySQL ≠ MariaDB : pas de
> `IF NOT EXISTS` dans les migrations.

### 5. Vérification
```bash
D=https://goodpractice.aureliengirard.fr
curl -s -o /dev/null -w "%{http_code}\n" "$D/"                              # 200 (SPA)
curl -s "$D/index.php/api/articles" | head -c 80; echo                      # JSON Hydra
curl -s -o /dev/null -w "%{http_code}\n" "$D/../.env.local"                 # PAS 200 (hors docroot)
# Login admin → doit renvoyer un token :
curl -s -X POST "$D/index.php/api/login_check" -H 'Content-Type: application/json' \
  -d '{"email":"admin@goodpractice.fr","password":"<mdp>"}' | head -c 80; echo
```
En cas de **500** : retire `ErrorDocument`/`SetEnvIf` du `public/.htaccess`.

## Mises à jour ultérieures
```bash
SSH="<user>@<ssh-host>" ./deploy/build.sh --push
ssh "$SSH" 'cd ~/projets/GoodPractice \
  && php8.2-cli composer.phar install --no-dev --optimize-autoloader \
  && php8.2-cli bin/console doctrine:migrations:migrate -n \
  && php8.2-cli bin/console cache:clear'
```

## Rollback
```bash
ssh "$SSH" 'cd ~/projets && rm -rf GoodPractice \
  && tar xzf ~/archives/goodpractice-legacy-<date>.tgz'
# puis re-pointer le docroot du sous-domaine sur l'ancien dossier
```

## Si le sous-domaine supporte `mod_rewrite`
Pour des URLs d'API propres (`/api` au lieu de `/index.php/api`) :
1. `frontend/.env.production` → `VITE_API_BASE=/api`, rebuild.
2. Installer le rewrite Symfony : `composer require symfony/apache-pack` (génère le
   `public/.htaccess` complet), et ajouter une route catch-all renvoyant `index.html`
   pour les deep-links SPA (sinon ils tombent sur le routeur Symfony en 404).

L'approche PATH_INFO retenue par défaut fonctionne **avec ou sans** rewrite et évite
ce conflit SPA/routeur — c'est le choix le plus robuste pour ce mutualisé.
