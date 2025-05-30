.PHONY: help
help:
	@printf "%-40s %s\n" "Target" "Description"
	@printf "%-40s %s\n" "------" "-----------"
	@make -pqR : 2>/dev/null \
        | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' \
        | sort \
        | egrep -v -e '^[^[:alnum:]]' -e '^$@$$' \
        | xargs -I _ sh -c 'printf "%-40s " _; make _ -nB | (grep -i "^# Help:" || echo "") | tail -1 | sed "s/^# Help: //g"'


.PHONY: run run-php8.1 run-php8.2 run-php8.3 run-php8.4
run-php8.1:
	@# Help: It creates and runs a docker image with PHP 8.1
	docker-compose run --rm php81 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.2:
	@# Help: It creates and runs a docker image with PHP 8.2
	docker-compose run --rm php82 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.3:
	@# Help: It creates and runs a docker image with PHP 8.3
	docker-compose run --rm php83 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run-php8.4:
	@# Help: It creates and runs a docker image with PHP 8.4
	docker-compose run --rm php84 bash -c "rm composer.lock || true; composer install --no-interaction; bash"
run: run-php8.1
	@# Help: It creates and runs a docker image with the lowest supported PHP version

.PHONY: phpstan phpstan-update-baseline
phpstan:
	@# Help: It runs PHPStan
	./vendor/bin/phpstan analyse src tests

phpstan-update-baseline:
	@# Help: It updates the PHPStan baseline
	./vendor/bin/phpstan analyse src tests --generate-baseline


.PHONY: test
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

ci: test phpstan cs-fix
	@# Help: It runs all tests and code style fix

ci-check: test phpstan cs-check
	@# Help: It runs all tests and code style check

.PHONY: rector
rector:
	@# Help: It runs rector
	./vendor/bin/rector
