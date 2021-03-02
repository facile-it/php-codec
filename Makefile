.PHONY: setup sh psalm usage test

usage:
	@echo "select target"

setup:
	docker-compose run php composer install

sh:
	docker-compose up -d
	docker-compose exec php bash

psalm:
	./vendor/bin/psalm src --no-cache

test:
	./vendor/bin/phpunit
