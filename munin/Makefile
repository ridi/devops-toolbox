BASE_DIR=$(shell dirname $(abspath $(lastword $(MAKEFILE_LIST))))
MUNIN_DIR=/etc/munin
EUID:=$(shell id -u -r)

.PHONY: all set-owner composer

all: set-owner composer

check:
ifneq ($(EUID),0)
	@echo "Please run as root user"
	@exit 1
endif

set-owner: check
	chown munin:munin $(MUNIN_DIR) $(MUNIN_DIR)/munin.conf $(BASE_DIR)/*.sh $(BASE_DIR)/asana_client/*.php

composer:
	composer install

test:
ifndef TEST_SERVER_NAME
	$(eval TEST_SERVER_NAME := $(shell read -p "Enter server name to test: " REPLY; echo $$REPLY))
endif
	php asana_client/create-uptime-task.php $(TEST_SERVER_NAME) 180.1 180 200

