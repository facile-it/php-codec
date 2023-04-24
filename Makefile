.PHONY: setup sh usage

usage:
	@echo "select target"

.PHONY: run run-php7.4 run-php8.0 run-php8.1 run-php8.2
run-php7.4:
	docker-compose run --rm php74 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.0:
	docker-compose run --rm php80 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.1:
	docker-compose run --rm php81 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.2:
	docker-compose run --rm php82 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run: run-php7.4

.PHONY: psalm psalm-update-baseline
psalm:
	./vendor/bin/psalm --no-cache

psalm-update-baseline:
	./vendor/bin/psalm --update-baseline


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

ci: test phpstan psalm type-assertions cs-fix

ci-check: test phpstan psalm type-assertions cs-check

.PHONY: rector
rector:
	./vendor/bin/rector
