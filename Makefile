.PHONY: help setup build up down destroy remake client api client-test client-test-coverage

DOCKER_COMPOSE = docker compose

default: help

help:
	@echo "make setup                - Build and start containers, initialize client and api"
	@echo "make build                - Build containers"
	@echo "make up                   - Start containers"
	@echo "make down                 - Stop and remove containers"
	@echo "make destroy              - Stop and remove containers, volumes, and images"
	@echo "make remake               - Recreate containers, volumes, and images"
	@echo "make client               - Access client container"
	@echo "make api                  - Access api container"
	@echo "make client-test          - Run tests in client container"
	@echo "make client-test-coverage - Run tests in client container with coverage"

setup:
	@make down
	@make build
	$(DOCKER_COMPOSE) run --rm client composer install
	cp api/app/.env.example api/app/.env
	$(DOCKER_COMPOSE) run --rm api php app/artisan key:generate
	@make up

build:
	$(DOCKER_COMPOSE) build

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

destroy:
	$(DOCKER_COMPOSE) down --rmi all --volumes --remove-orphans

remake:
	@make destroy
	@make setup

client:
	$(DOCKER_COMPOSE) exec client bash

api:
	$(DOCKER_COMPOSE) exec api bash

client-test:
	$(DOCKER_COMPOSE) exec client vendor/bin/phpunit tests/

client-test-coverage:
	$(DOCKER_COMPOSE) exec client vendor/bin/phpunit --coverage-html coverage tests/
