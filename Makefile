.PHONY: help
help:
	@printf "%-40s %s\n" "Target" "Description"
	@printf "%-40s %s\n" "------" "-----------"
	@make -pqR : 2>/dev/null \
        | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' \
        | sort \
        | egrep -v -e '^[^[:alnum:]]' -e '^$@$$' \
        | xargs -I _ sh -c 'printf "%-40s " _; make _ -nB | (grep -i "^# Help:" || echo "") | tail -1 | sed "s/^# Help: //g"'


.PHONY: run run-php7.4 run-php8.0 run-php8.1 run-php8.2
run-php7.4:
	@# Help: It creates and runs a docker image with PHP 7.4
	docker compose run --rm php74 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.0:
	@# Help: It creates and runs a docker image with PHP 8.0
	docker-compose run --rm php80 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.1:
	@# Help: It creates and runs a docker image with PHP 8.1
	docker-compose run --rm php81 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.2:
	@# Help: It creates and runs a docker image with PHP 8.2
	docker-compose run --rm php82 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run: run-php7.4
	@# Help: It creates and runs a docker image with the lowest supported PHP version

.PHONY: psalm psalm-update-baseline
psalm:
	@# Help: It runs Psalm
	./vendor/bin/psalm --no-cache

psalm-update-baseline:
	@# Help: It updates the Psalm baseline
	./vendor/bin/psalm --update-baseline


.PHONY: phpstan phpstan-update-baseline
phpstan:
	@# Help: It runs PHPStan
	./vendor/bin/phpstan analyse src tests

phpstan-update-baseline:
	@# Help: It updates the PHPStan baseline
	./vendor/bin/phpstan analyse src tests --generate-baseline


.PHONY: type-assertions test
type-assertions:
	@# Help: It runs tests on Psalm types
	./vendor/bin/psalm tests/type-assertions --no-cache

test:
	@# Help: It runs PHPUnit tests
	XDEBUG_MODE=coverage ./vendor/bin/phpunit

.PHONY: ci ci-check cs-check cs-fix
cs-fix:
	@# Help: It runs the code style fix
	./vendor/bin/php-cs-fixer fix --ansi --verbose

cs-check:
	@# Help: It runs the code style check
	./vendor/bin/php-cs-fixer fix --ansi --verbose --dry-run

ci: test phpstan psalm type-assertions cs-fix
	@# Help: It runs all tests and code style fix

ci-check: test phpstan psalm type-assertions cs-check
	@# Help: It runs all tests and code style check

.PHONY: rector
rector:
	@# Help: It runs rector
	./vendor/bin/rector
