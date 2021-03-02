# Php-codec

Php-codec is a partial porting of [io-ts](https://github.com/gcanti/io-ts) in PHP.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)

[![CI](https://github.com/ilario-pierbattista/php-codec/actions/workflows/ci.yaml/badge.svg?branch=master&event=push)](https://github.com/ilario-pierbattista/php-codec/actions/workflows/ci.yaml)
[![Static analysis](https://github.com/ilario-pierbattista/php-codec/actions/workflows/static-analysis.yaml/badge.svg?branch=master&event=push)](https://github.com/ilario-pierbattista/php-codec/actions/workflows/static-analysis.yaml)
[![codecov](https://codecov.io/gh/ilario-pierbattista/php-codec/branch/master/graph/badge.svg?token=HP4OFEEPY6)](https://codecov.io/gh/ilario-pierbattista/php-codec)

## Installation

    composer require pybatt/php-codec

## TODO

- [ ] Find a way to avoid exposing codecs implementation
- [ ] Error reporting in case of `ClassFromArrayType` and `ArrayType` is too poor (all success or general error approach)
- [ ] I need a way for piping codecs, eg: `(Type<A, I, A>, Type<B, A, B>) => Type<B, I, B>`
