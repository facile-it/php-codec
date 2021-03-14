.PHONY: setup sh psalm usage test type-assertions architecture

usage:
	@echo "select target"

setup:
	docker-compose run php composer install

sh:
	docker-compose up -d
	docker-compose exec php bash

psalm:
	./vendor/bin/psalm src --no-cache

type-assertions:
	./vendor/bin/psalm tests/type-assertions --no-cache

test:
	./vendor/bin/phpunit

architecture:
	./vendor/bin/phpat

.PHONY: ci
ci: test psalm type-assertions architecture
