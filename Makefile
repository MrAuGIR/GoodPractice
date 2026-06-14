include .env
export

# ==========================================
# Docker
# ==========================================

start:
	docker compose up -d

stop:
	docker compose down

build:
	docker compose build --no-cache

ps:
	docker compose ps

logs:
	docker compose logs -f

# ==========================================
# Accès shell
# ==========================================

bash:
	docker exec -ti $(PROJECT_NAME)_backend /bin/bash

# ==========================================
# Console Symfony (backend)
# ==========================================

cc:
	docker exec -ti $(PROJECT_NAME)_backend php bin/console cache:clear

console:
	docker exec -ti $(PROJECT_NAME)_backend php bin/console $(c)

composer:
	docker exec -ti $(PROJECT_NAME)_backend composer $(c)

# ==========================================
# Base de données
# ==========================================

db-create:
	docker exec -ti $(PROJECT_NAME)_backend php bin/console doctrine:database:create

migration:
	docker exec -ti $(PROJECT_NAME)_backend php bin/console make:migration

migrate:
	docker exec -ti $(PROJECT_NAME)_backend php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker exec -ti $(PROJECT_NAME)_backend php bin/console doctrine:fixtures:load --no-interaction

# ==========================================
# Frontend (Vue 3)
# ==========================================

front-install:
	docker exec -ti $(PROJECT_NAME)_frontend npm install

front-bash:
	docker exec -ti $(PROJECT_NAME)_frontend sh

.PHONY: start stop build ps logs bash cc console composer db-create migration migrate fixtures front-install front-bash
