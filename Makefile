.PHONY: run tests

DOCKER_COMPOSE ?= docker compose
DOCKER_USER ?= "$(shell id -u):$(shell id -g)"
ENV ?= "dev"
PHP_CONT ?= $(DOCKER_COMPOSE) exec php
SYMFONY ?= $(PHP_CONT) bin/console

init:
	@make -s docker-compose-check
	@if [ ! -e compose.override.yml ]; then \
		cp compose.override.dist.yml compose.override.yml; \
	fi
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) run --rm php composer install --no-interaction --no-scripts
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) run --rm nodejs
	@make -s install
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) up -d

run:
	@make -s up

debug:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) -f compose.yml -f compose.override.yml -f compose.debug.yml up -d

up:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) up -d

down:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) down

stop:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) stop

install:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) run --rm php bin/console sylius:install -s default -n

clean:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) down -v

php-shell:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) exec php sh

node-shell:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) run --rm -i nodejs sh

node-watch:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) run --rm -i nodejs "npm run watch"

docker-compose-check:
	@which $(DOCKER_COMPOSE) > /dev/null || (echo "Please install docker compose binary" && exit 1)
	@echo "You are using \"$(DOCKER_COMPOSE)\" binary"
	@echo "Current version is \"$$($(DOCKER_COMPOSE) version)\""

php-lint:
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(PHP_CONT) ./vendor/bin/phpstan analyse --memory-limit=-1
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(PHP_CONT) ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix src
	@ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(PHP_CONT) ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix tests

tests:
	@DOCKER_USER=$(DOCKER_USER) $(SYMFONY) cache:clear --env=test
	@DOCKER_USER=$(DOCKER_USER) $(SYMFONY) doctrine:database:drop --if-exists --force --env=test
	@DOCKER_USER=$(DOCKER_USER) $(SYMFONY) doctrine:database:create --env=test
	@DOCKER_USER=$(DOCKER_USER) $(SYMFONY) doctrine:migrations:migrate -n --env=test
	@DOCKER_USER=$(DOCKER_USER) $(SYMFONY) sylius:fixtures:load -n --env=test
	@DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE) exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit -d memory_limit=-1 --coverage-html var/reports || true
