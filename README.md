# Php-codec
Php-codec is a partial porting of [io-ts](https://github.com/gcanti/io-ts) in PHP.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)

[![CI](https://github.com/facile-it/php-codec/actions/workflows/ci.yaml/badge.svg?branch=master&event=push)](https://github.com/facile-it/php-codec/actions/workflows/ci.yaml)
[![Static analysis](https://github.com/facile-it/php-codec/actions/workflows/static-analysis.yaml/badge.svg?branch=master&event=push)](https://github.com/facile-it/php-codec/actions/workflows/static-analysis.yaml)
[![codecov](https://codecov.io/gh/facile-it/php-codec/branch/master/graph/badge.svg?token=HP4OFEEPY6)](https://codecov.io/gh/facile-it/php-codec) [![Join the chat at https://gitter.im/php-codec/community](https://badges.gitter.im/php-codec/community.svg)](https://gitter.im/php-codec/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Disclaimer

This project is under active development: it's unstable and still poorly documented.
The APIs are likely to change several times, and they won't be ready for production soon.

The project follows [semantic versioning](https://semver.org/).

## Installation

    composer require facile-it/php-codec

## Introduction

This project is a partial porting of the fantastic [io-ts](https://github.com/gcanti/io-ts) library for Typescript.
Everything rounds about the concept of decoder, encoder and codec.

Decoders are capable of transform values from one type to another one. This transformation may fail.

```php
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template I
 * @psalm-template A
 */
interface Decoder {
     /**
     * @psalm-param I $i
     * @psalm-return Validation<A>
     */
    public function decode($i): Validation;
    
    /** ... */
}
```

Encoders do a similar transformation but between types such that it cannot fail.

```php
/**
 * @psalm-template A
 * @psalm-template O
 */
interface Encoder
{
    /**
     * @psalm-param A $a
     * @psalm-return O
     */
    public function encode($a);
}
```

Codecs are a combination of a decoder and an encoder, putting together their features.

I recommend reading the [The Idea](https://github.com/gcanti/io-ts/blob/master/index.md#the-idea) section from the 
documentation of io-ts. It starts with a beautiful description of what codecs are.
> A value of type `Type<A, O, I>` (called "codec") is the runtime representation of the static type `A`.

## Getting started

- this could become the main section of this doc

## Decoders

- how to build decoders
- how to decode 
- how to deal with the output

Decoders are objects with decoding capabilities.
A decoder of type `Decoder<I, A>` takes an input of type `I` and builds a result of type `Validation<A>`.

The class `Facile\PhpCodec\Decoders` provides a list of built-in decoders.

## Examples

Take a look to the [examples](https://github.com/facile-it/php-codec/tree/master/tests/examples) folder.
