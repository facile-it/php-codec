# Php-codec

Php-codec is a partial porting of [io-ts](https://github.com/gcanti/io-ts) in PHP.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)

[![CI](https://github.com/ilario-pierbattista/php-codec/actions/workflows/ci.yaml/badge.svg?branch=master&event=push)](https://github.com/ilario-pierbattista/php-codec/actions/workflows/ci.yaml)
[![Static analysis](https://github.com/ilario-pierbattista/php-codec/actions/workflows/static-analysis.yaml/badge.svg?branch=master&event=push)](https://github.com/ilario-pierbattista/php-codec/actions/workflows/static-analysis.yaml)
[![codecov](https://codecov.io/gh/ilario-pierbattista/php-codec/branch/master/graph/badge.svg?token=HP4OFEEPY6)](https://codecov.io/gh/ilario-pierbattista/php-codec)

## Installation

    composer require pybatt/php-codec

## Introduction

This is a partial porting of the fantastic [io-ts](https://github.com/gcanti/io-ts) library for Typescript.

> A value of type `Type<A, O, I>` (called "codec") is the runtime representation of the static type `A`.

I strongly recomend the reading of [The Idea](https://github.com/gcanti/io-ts/blob/master/index.md#the-idea) section
from the io-ts documentation.

## Types and combinators

All the implemented codecs and combinators are exposed through methods of the class `Pybatt\Codec\Codecs`.

| Typescript Type | Psalm Type | Codec | 
| --- | --- | --- |
| `unknown` | `mixed` | TODO |
| `null` | `null` | `Codecs::null()` |
| `bool` | `bool` | `Codecs::bool()` |
| `number` | `int` | `Codecs::int()` |
| `number` | `float` | `Codecs::float()` |
| `string` | `string` | `Codecs::string()` |
| `'s'` | `'s'` | `Codecs::litteral('s')` |
| `Array<T>` | `list<T>` | `Codecs::listt(Type $item)` |
| - | `A::class` | `Codecs::classFromArray(Type[] $props, callable $factory, A::class)` |

## Examples

For further examples take a look to the [examples](https://github.com/ilario-pierbattista/php-codec/tree/docs/tests/examples):

```php
use Pybatt\Codec\Codecs;

$codec = Codecs::classFromArray(
    [
        'a' => Codecs::string(),
        'b' => Codecs::int(),
        'c' => Codecs::bool(),
        'd' => Codecs::float()
    ],
    function (string $a, int $b, bool $c, float $d): Foo {
        return new Foo($a, $b, $c, $d);
    },
    Foo::class
);

// Gives an instance of ValidationSuccess<Foo>
$validation = $codec->decode(['a' => 'hey', 'b' => 123, 'c' => false, 'd' => 1.23]);

// Gives an instance of ValidationFailures
$failures = $codec->decode(['a' => 'hey', 'b' => 123, 'c' => 'a random string', 'd' => 1.23]);

class Foo { 
    public function __construct(
        string $a,
        int $b,
        bool $c,
        float $d
    ) {
    // [...]
    }
}
```

