.PHONY: setup sh psalm usage test type-assertions architecture

usage:
	@echo "select target"

setup:
	docker-compose run php composer install

sh:
	docker-compose up -d
	docker-compose exec php bash

psalm:
	./vendor/bin/psalm --no-cache

type-assertions:
	./vendor/bin/psalm tests/type-assertions --no-cache

test:
	./vendor/bin/phpunit

architecture:
	./vendor/bin/phpat

cs-fix:
	./vendor/bin/php-cs-fixer fix --ansi --verbose

cs-check:
	./vendor/bin/php-cs-fixer fix --ansi --verbose --dry-run

.PHONY: ci
ci: test cs-check psalm type-assertions architecture
