#!/usr/bin/env bash

include .env
export $(shell sed 's/=.*//' .env)

DOCKER_COMPOSE = docker compose -p $(PROJECT_NAME)

CONTAINER_PHP := $(shell docker container ls -f "name=$(PROJECT_NAME)-php" -q)
CONTAINER_DB := $(shell docker container ls -f "name=$(PROJECT_NAME)-database" -q)
CONTAINER_QA := $(shell docker container ls -f "name=$(PROJECT_NAME)-qa" -q)

PHP := docker exec -ti $(CONTAINER_PHP)
DATABASE := docker exec -ti $(CONTAINER_DB)
QA := docker exec -ti $(CONTAINER_QA)

init: install update npm fabric db jwt

## Kill all containers
kill:
	@$(DOCKER_COMPOSE) kill $(CONTAINER) || true

## Build containers
build:
	@$(DOCKER_COMPOSE) build --pull --no-cache

## Init project
init: install update

## Start containers
start:
	@$(DOCKER_COMPOSE) up -d

## Stop containers
stop:
	@$(DOCKER_COMPOSE) down

restart: stop start

## Init project
init: install update npm fabric db

npm: 
	$(PHP) npm install
	$(PHP) npm run build

cache:
	$(PHP) rm -r var/cache

## Entering php shell
php:
	@$(DOCKER_COMPOSE) exec php sh

## Entering database shell
database:
	@$(DOCKER_COMPOSE) exec database sh

## Composer install
install:
	$(PHP) composer install

## Composer update
update:
	$(PHP) composer update

fabric: 
	$(PHP) php bin/console messenger:setup-transports

db: 
	$(PHP) php bin/console doctrine:database:drop -f --if-exists
	$(PHP) php bin/console doctrine:database:create
	$(PHP) php bin/console doctrine:schema:update -f
	$(PHP) php bin/console hautelook:fixtures:load -n
	$(PHP) php bin/console fos:elastica:delete
	$(PHP) php bin/console fos:elastica:create
	$(PHP) php bin/console fos:elastica:populate

jwt:
	$(PHP) php bin/console lexik:jwt:generate-keypair --skip-if-exists

trust-cert:
	@echo "Installing local SSL certificate..."
	@docker cp $(CONTAINER_PHP):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt
	@if [ "$$(uname)" = "Darwin" ]; then \
		echo "Detected macOS. Installing certificate..."; \
		sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt; \
		echo "Certificate installed successfully!"; \
	elif [ "$$(uname)" = "Linux" ]; then \
		echo "Detected Linux. Installing certificate..."; \
		sudo cp /tmp/root.crt /usr/local/share/ca-certificates/root.crt; \
		sudo update-ca-certificates; \
		echo "Certificate installed successfully!"; \
	elif [ "$$(uname)" = "MINGW64_NT" ] || [ "$$(uname)" = "MINGW32_NT" ]; then \
		echo "Detected Windows. Opening certificate installer..."; \
		certutil -addstore -f "ROOT" /tmp/root.crt; \
		echo "Certificate installed successfully!"; \
	else \
		echo "Unknown operating system. Please install the certificate manually from: /tmp/root.crt"; \
	fi
	@rm /tmp/root.crt

migration:
	$(PHP) php bin/console make:migration

migrate:
	$(PHP) php bin/console doctrine:migration:migrate

fixture:
	$(PHP) php bin/console hautelook:fixtures:load -n
	$(PHP) php bin/console fos:elastica:delete
	$(PHP) php bin/console fos:elastica:create
	$(PHP) php bin/console fos:elastica:populate

schema:
	$(PHP) php bin/console doctrine:schema:update -f

regenerate:
	$(PHP) php bin/console make:entity --regenerate App

entity:
	$(PHP) php bin/console make:entity

message:
	$(PHP) php bin/console make:message

command:
	$(PHP) php bin/console make:command

dotenv:
	$(PHP) php bin/console debug:dotenv

php-cs-fixer:
	$(PHP) ./vendor/bin/php-cs-fixer fix src --rules=@Symfony --verbose --diff

php-stan:
	$(PHP) ./vendor/bin/phpstan analyse src -l $(or $(level), 8) --memory-limit=-1

consume:
	$(PHP) php bin/console messenger:consume async -vv

transform-subtitle:
	$(PHP) php bin/console transform-subtitle

resume-video:
	$(PHP) php bin/console resume-video

unzip:
	cat  public/debug/1bba6dc7-21ed-41c2-9694-6a2ea4db41fd.zip.part* > public/debug/1bba6dc7-21ed-41c2-9694-6a2ea4db41fd.zip
	unzip public/debug/1bba6dc7-21ed-41c2-9694-6a2ea4db41fd.zip -d public/debug/
	rm -r public/debug/__MACOSX
	rm -r public/debug/1bba6dc7-21ed-41c2-9694-6a2ea4db41fd.zip

elastica:
	$(PHP) php bin/console fos:elastica:delete
	$(PHP) php bin/console fos:elastica:create
	$(PHP) php bin/console fos:elastica:populate