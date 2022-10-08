.PHONY: setup sh psalm usage test type-assertions architecture

usage:
	@echo "select target"

setup:
	docker-compose run php composer install --no-interaction

sh:
	docker-compose up -d
	docker-compose exec php bash

psalm-src:
	./vendor/bin/psalm src --no-cache

psalm-tests:
	./vendor/bin/psalm tests --no-cache

psalm: psalm-src psalm-tests

.PHONY: phpstan-src phpstan-tests phpstan
phpstan-src:
	./vendor/bin/phpstan analyse src

phpstan-tests:
	./vendor/bin/phpstan analyse tests

phpstan: phpstan-src phpstan-tests

type-assertions:
	./vendor/bin/psalm tests/type-assertions --no-cache

test:
	./vendor/bin/phpunit

architecture:
	./vendor/bin/phpat

.PHONY: ci cs-check cs-fix
cs-fix:
	./vendor/bin/php-cs-fixer fix --ansi --verbose

cs-check:
	./vendor/bin/php-cs-fixer fix --ansi --verbose --dry-run

ci: test cs-fix psalm type-assertions architecture

ci-check: test cs-check psalm type-assertions architecture
