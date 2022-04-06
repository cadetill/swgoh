current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

.PHONY: start
start:
	docker compose up -d

.PHONY: test
test:
	@docker compose exec php make run-tests
