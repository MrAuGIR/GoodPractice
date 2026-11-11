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

# ==========================================
# Qualité & tests (non interactif : utilisable par le hook pre-push)
# ==========================================

BACK = docker exec $(PROJECT_NAME)_backend
FRONT = docker exec $(PROJECT_NAME)_frontend

cs:
	$(BACK) composer cs-check

cs-fix:
	$(BACK) composer cs-fix

phpstan:
	$(BACK) composer phpstan

test-back:
	$(BACK) composer test

test-back-coverage:
	$(BACK) composer test-coverage
	$(BACK) composer coverage-check

test-front:
	$(FRONT) npm run test

test-front-coverage:
	$(FRONT) npm run test:coverage

quality: cs phpstan

test: test-back test-front

check: quality test-back-coverage test-front-coverage

hooks-install:
	chmod +x .githooks/*
	git config core.hooksPath .githooks
	@echo "Hooks git installés (.githooks)."

.PHONY: start stop build ps logs bash cc console composer db-create migration migrate fixtures front-install front-bash \
	cs cs-fix phpstan test-back test-back-coverage test-front test-front-coverage quality test check hooks-install
