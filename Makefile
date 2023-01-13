THIS_FILE := $(abspath $(lastword $(MAKEFILE_LIST)))
CURRENT_DIR := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))
#export COMPOSE_FILE=compose-dev.yml
#export COMPOSE_PROJECT_NAME=memcachedadmin-dev
export DOCKER_BUILDKIT?=1
export COMPOSE_CONVERT_WINDOWS_PATHS?=1
export TZ?=UTC
.EXPORT_ALL_VARIABLES:
.PHONY: *

help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[32m%-14s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Docker compose up
	docker-compose up --build --no-deps --detach --remove-orphans

down: ## Docker compose down
	docker-compose down --remove-orphans

stop: ## Docker compose stop
	docker-compose stop

update: ## Docker compose pull
	docker-compose pull
	make up

restart: ## Restart containers
	make down
	make up
	$(info Restart completed)

state: ## Show current state
	docker ps --format=table

logs: ## Show docker logs
	docker-compose logs -f --tail=100 $(ARGS)

app: ## PHP Container shell
	docker-compose exec memcachedadmin bash
