# GoodPractice

[![CI](https://github.com/MrAuGIR/GoodPractice/actions/workflows/ci.yml/badge.svg)](https://github.com/MrAuGIR/GoodPractice/actions/workflows/ci.yml)

**🔗 Démo en ligne : [goodpractice.aureliengirard.fr](https://goodpractice.aureliengirard.fr)**

Site de démonstration en cours de modernisation : refonte d'un projet d'apprentissage
de 2021 en une vitrine technique (API REST + SPA).

## Stack

| Couche      | Techno                                  |
|-------------|-----------------------------------------|
| Backend     | Symfony 7 + API Platform (API REST)     |
| Frontend    | Vue 3 + Vite + TypeScript (SPA)         |
| Base        | MySQL 8                                  |
| Infra local | Docker / Docker Compose                 |

> L'ancien site PHP « maison » de 2021 est conservé dans `legacy/` comme référence
> fonctionnelle pour la migration.

## Structure

```
.
├── backend/        # API Symfony 7 (docroot : public/)
├── frontend/       # SPA Vue 3 (Vite)
├── docker/php/     # Image PHP-Apache (Dockerfile, vhost, xdebug)
├── docker-compose.yml
├── Makefile
├── legacy/         # ancien site 2021 (référence)
└── plan.md         # roadmap de modernisation
```

## Démarrage local (Docker)

Prérequis : Docker + Docker Compose.

```bash
make build      # build de l'image backend (php:8.2-apache)
make start      # démarre db, phpmyadmin, maildev, backend, frontend
make ps         # état des conteneurs
make logs       # logs en continu
make stop       # arrêt
```

### Services exposés

| Service     | URL                       | Rôle                         |
|-------------|---------------------------|------------------------------|
| backend     | http://localhost:8000     | API Symfony                  |
| frontend    | http://localhost:5173     | SPA Vue (proxy `/api`)       |
| phpMyAdmin  | http://localhost:8080     | administration MySQL         |
| maildev     | http://localhost:8025     | capture des mails (dev)      |

### Commandes utiles

```bash
make bash               # shell dans le conteneur backend
make console c="..."    # bin/console (ex: make console c=cache:clear)
make composer c="..."   # composer dans le conteneur
make db-create          # création de la base
make migrate            # exécute les migrations Doctrine
make front-install      # npm install dans le conteneur frontend
```

La config (identifiants MySQL, nom de projet) est centralisée dans `.env` — aucun
identifiant en dur dans le code.

## Tests, qualité & CI

Tout s'exécute **dans les conteneurs** (`make start` au préalable).

```bash
make quality              # PHP-CS-Fixer (dry-run) + PHPStan niveau 6
make cs-fix               # corrige le style PHP
make test                 # PHPUnit (backend) + Vitest (frontend)
make test-back-coverage   # PHPUnit + couverture + seuil bloquant (70 %)
make test-front-coverage  # Vitest + couverture + seuils bloquants
make check                # tout : qualité + tests + couverture
```

| Outil         | Config                              | Exigence                         |
|---------------|-------------------------------------|----------------------------------|
| PHP-CS-Fixer  | `backend/.php-cs-fixer.dist.php`    | `@Symfony` + `@Symfony:risky`    |
| PHPStan       | `backend/phpstan.dist.neon`         | niveau 6, extensions Symfony/Doctrine/PHPUnit |
| PHPUnit       | `backend/phpunit.dist.xml`          | couverture lignes ≥ 70 % (`bin/coverage-check.php`) |
| Vitest        | `frontend/vite.config.ts`           | lignes/fonctions/statements ≥ 70 %, branches ≥ 60 % |

Rapports HTML de couverture : `backend/var/coverage/html/index.html` et
`frontend/coverage/index.html`.

Organisation des tests backend : `tests/Unit` (PHP pur, mocks), `tests/Integration`
(container Symfony + BDD : commandes, importeur), `tests/Api` (fonctionnels HTTP via
`ApiTestCase`). Côté frontend : `src/**/__tests__/*.spec.ts`, helpers partagés dans
`src/__tests__/helpers.ts`.

### Hook git pre-push

À installer **une fois** après le clone :

```bash
make hooks-install   # git config core.hooksPath .githooks
```

Avant chaque `git push`, `.githooks/pre-push` lance PHP-CS-Fixer, PHPStan, PHPUnit et
Vitest (avec seuils de couverture) dans Docker ; le push est annulé au premier échec.
Contournement d'urgence : `git push --no-verify`.

### Intégration continue

Sur chaque PR vers `master` et chaque merge dans `master`,
[GitHub Actions](https://github.com/MrAuGIR/GoodPractice/actions/workflows/ci.yml) lance
4 jobs : qualité backend, tests + couverture backend (MySQL), type-check + build frontend,
tests + couverture frontend. Les rapports de couverture sont publiés en artefacts et
résumés dans l'onglet *Summary* du run.
