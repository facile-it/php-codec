.PHONY: setup sh usage

usage:
	@echo "select target"

setup:
	docker-compose run php composer install --no-interaction

sh:
	docker-compose up -d
	docker-compose exec php bash


.PHONY: psalm psalm-src psalm-tests psalm-update-baseline

psalm-src:
	./vendor/bin/psalm src --no-cache

psalm-tests:
	./vendor/bin/psalm tests --no-cache

psalm: psalm-src psalm-tests

psalm-update-baseline:
	./vendor/bin/psalm --update-baseline src tests


.PHONY: phpstan phpstan-update-baseline

phpstan:
	./vendor/bin/phpstan analyse src tests

phpstan-update-baseline:
	./vendor/bin/phpstan analyse src tests --generate-baseline


.PHONY: type-assertions test

type-assertions:
	./vendor/bin/psalm tests/type-assertions --no-cache

test:
	./vendor/bin/phpunit

.PHONY: ci cs-check cs-fix
cs-fix:
	./vendor/bin/php-cs-fixer fix --ansi --verbose

cs-check:
	./vendor/bin/php-cs-fixer fix --ansi --verbose --dry-run

ci: test cs-fix psalm type-assertions

ci-check: test cs-check psalm type-assertions
