BASENAME=$(shell bin/_basename.sh)
COMPOSE_FILES=$(shell bash -c ". ./bin/support.sh; get-compose-file-string")
DOCKER_INSIDE=$(shell bash -c ". ./bin/support.sh; in_container_inside")

default: bash

# Блокирует выполнение комманды внутри контейнера
_lock_container_execute:
    ifeq ($(DOCKER_INSIDE), 1)
	    $(error "Запрещено выполнять эту операцию внутри контейнера")
	    exit 1
    endif

# Запуск консоли в контейнере, или выполнение команды в контейнере. (запуск с ключем script="echo \"test\"")
bash: _lock_container_execute
	$(if ${script}, \
        docker-compose $(COMPOSE_FILES) exec fpm bash -c "$(script)", \
        docker-compose $(COMPOSE_FILES) exec fpm bash \
    )

build: _lock_container_execute
	docker-compose $(COMPOSE_FILES) build

ps: _lock_container_execute
	docker-compose $(COMPOSE_FILES) ps -s

clean: _lock_container_execute
	docker-compose $(COMPOSE_FILES) rm -fv;

agent-start:
	bash -c ". ./bin/support.sh; ssh-agent-start;"

start: _lock_container_execute agent-start
	docker-compose $(COMPOSE_FILES) up -d;

stop: _lock_container_execute
	docker-compose $(COMPOSE_FILES) down;

restart: _lock_container_execute stop start

# Починка докера (при некорректном завершении)
docker_repair: _lock_container_execute
	$(info "Починка Docker контейнера")
	sudo rm -Rf /var/lib/docker/network
	sudo service docker start
