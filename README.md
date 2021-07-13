# Php-codec

Php-codec is a partial porting of [io-ts](https://github.com/gcanti/io-ts) in PHP.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)

[![CI](https://github.com/facile-it/php-codec/actions/workflows/ci.yaml/badge.svg?branch=master&event=push)](https://github.com/facile-it/php-codec/actions/workflows/ci.yaml)
[![Static analysis](https://github.com/facile-it/php-codec/actions/workflows/static-analysis.yaml/badge.svg?branch=master&event=push)](https://github.com/facile-it/php-codec/actions/workflows/static-analysis.yaml)
[![codecov](https://codecov.io/gh/facile-it/php-codec/branch/master/graph/badge.svg?token=HP4OFEEPY6)](https://codecov.io/gh/facile-it/php-codec) [![Join the chat at https://gitter.im/php-codec/community](https://badges.gitter.im/php-codec/community.svg)](https://gitter.im/php-codec/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Disclaimer

This project is under active development.
It's very unstable and poorly documented.
The APIs are likely to change several times, and it won't be ready for production in the near future.

This project follows the [semantic versioning](https://semver.org/).

## Installation

    composer require facile-it/php-codec

## Introduction

This is a partial porting of the fantastic [io-ts](https://github.com/gcanti/io-ts) library for Typescript.
Its documentation starts with:

> A value of type `Type<A, O, I>` (called "codec") is the runtime representation of the static type `A`.

I strongly recomend the reading of [The Idea](https://github.com/gcanti/io-ts/blob/master/index.md#the-idea) section
from the io-ts documentation.

## Decoders

Decoders are objects with decoding capabilities.
A decoder of type `Decoder<I, A>` takes an input of type `I` and builds a result of type `Validation<A>`.

The class `Facile\PhpCodec\Decoders` provides a list of built-in decoders.

## Examples

Take a look to the [examples](https://github.com/facile-it/php-codec/tree/master/tests/examples) folder.
