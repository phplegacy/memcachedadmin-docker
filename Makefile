THIS_FILE := $(abspath $(lastword $(MAKEFILE_LIST)))
CURRENT_DIR := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))
export COMPOSE_FILE=docker-compose-dev.yml
export COMPOSE_PROJECT_NAME=memcachedadmin-dev

up:
	docker-compose up --build --no-deps --detach --remove-orphans

down:
	docker-compose down --remove-orphans

stop:
	docker-compose stop

update:
	docker-compose pull
	make up

restart: down up
	$(info Restart completed)

state:
	docker ps --format=table

logs: ## Show docker logs
	docker-compose logs -f --tail=100 $(ARGS)

php:
	docker-compose exec memcachedadmin bash
