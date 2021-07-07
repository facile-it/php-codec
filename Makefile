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

psalm-examples:
	./vendor/bin/psalm tests/examples --no-cache

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
