.PHONY: setup sh usage

usage:
	@echo "select target"

.PHONY: run-php7.4 run-php8.0 run-php8.1 run-php8.2
run-php7.4:
	docker-compose run --rm php74 bash -c "composer install --no-interaction; bash"
run-php8.0:
	docker-compose run --rm php80 bash -c "composer install --no-interaction; bash"
run-php8.1:
	docker-compose run --rm php81 bash -c "composer install --no-interaction; bash"
run-php8.2:
	docker-compose run --rm php82 bash -c "composer install --no-interaction; bash"
run: run-php7.4

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

.PHONY: ci ci-check cs-check cs-fix
cs-fix:
	./vendor/bin/php-cs-fixer fix --ansi --verbose

cs-check:
	./vendor/bin/php-cs-fixer fix --ansi --verbose --dry-run

ci: test cs-fix psalm type-assertions

ci-check: test cs-check psalm type-assertions
