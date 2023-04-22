# Contributing to PHP Codec


## Dev environment

PHP Codec comes with a containerized environment in order to make
contribution really easy.
The container comes with `composer` and `xdebug` installed.

Run

```shell
make run
```

and you're in. By default, `run` will build and start the container with the
lowest PHP version supported.
To start a container with a different version, choose one of the other targets.

```shell
make run-php7.4 # *default
make run-php8.0
make run-php8.1
make run-php8.2
```

Each `run-php*` target will remove the `composer.lock` file and execute a 
clean `composer install`.

To execute all the testing targets, run the following from the inside 
of the container:

```shell
make ci
```

It will run PHPUnit tests, PHPStan, Psalm and the code style checker.
Consult `Makefile` to learn which are the other targets that allow to run 
those checks singularly.


## How to contribute

Open an issue before each non-trivial PR.
Describe what are your needs and your proposal, if any.
