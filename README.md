# GoodPractice

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
