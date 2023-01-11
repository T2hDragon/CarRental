# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console
YARN = $(PHP_CONT) yarn

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

phpcs:
	@$(PHP) ./vendor/php-parallel-lint/php-parallel-lint/parallel-lint src
	@$(PHP) ./vendor/squizlabs/php_codesniffer/bin/phpcs -s
.PHONY: phpcs

phpcbf:
	$(PHP) ./vendor/php-parallel-lint/php-parallel-lint/parallel-lint src
	$(PHP) ./vendor/squizlabs/php_codesniffer/bin/phpcbf
.PHONY: phpcbf

phpstan:
	$(PHP) ./vendor/bin/phpstan analyze -c phpstan.neon --memory-limit=512M
.PHONY: phpstan

lint:
	@${MAKE} phpcs
	@${MAKE} phpstan
	$(SYMFONY) lint:twig templates
	$(SYMFONY) lint:yaml config --parse-tags
	@${MAKE} doctrine-validate
.PHONY: lint

new-migration:
	$(SYMFONY) make:migration
.PHONY: new-migration

update-database:
	$(SYMFONY) doctrine:migrations:migrate --no-interaction
	${MAKE} cc
.PHONY: update-database

rebuild:
	$(SYMFONY) doctrine:schema:drop --force
	$(SYMFONY) doctrine:schema:drop --force --full-database
	$(SYMFONY) doctrine:migrations:migrate --no-interaction
	$(SYMFONY) doctrine:fixtures:load
	#$(SYMFONY) hautelook:fixtures:load --no-interaction
	$(YARN) install
	${MAKE} assets
	${MAKE} cc
.PHONY: rebuild-database

doctrine-validate:
	$(SYMFONY) app:doctrine-validate
.PHONY: doctrine-validate

revert-last-migration:
	$(SYMFONY) doctrine:migrations:migrate prev
	${MAKE} cc
.PHONY: revert-last-migration

assets:
	$(YARN) dev
.PHONY: assets

watch-assets:
	$(YARN) watch
.PHONY: watch-assets

install:
	$(YARN) add $(package)
.PHONY: install

uninstall:
	$(YARN) remove $(package)
.PHONY: install
