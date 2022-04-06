current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

.PHONY: run-test

run-tests:
	./vendor/bin/pest

.PHONY: deps
deps: composer-install

.PHONY: composer-install
composer-install: CMD=install

.PHONY: composer-update
composer-update: CMD=update

.PHONY: composer-require
composer-require: CMD=require
composer-require: INTERACTIVE=-ti --interactive
composer-require: DEV=

.PHONY: composer-require-dev
composer-require-dev: CMD=require
composer-require-dev: INTERACTIVE=-ti --interactive
composer-require-dev: DEV=--dev

.PHONY: composer
composer composer-install composer-update composer-require composer-require-dev composer-require-module:
	@docker run --rm $(INTERACTIVE) --volume $(current-dir):/app --user $(id -u):$(id -g) \
		composer:2.2.6 $(CMD) $(DEV) \
			--ignore-platform-reqs \
			--no-ansi
