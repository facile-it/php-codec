# PHP-codec
PHP-codec is a partial porting of [io-ts](https://github.com/gcanti/io-ts) in PHP.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

[![CI](https://github.com/facile-it/php-codec/actions/workflows/ci.yaml/badge.svg?branch=master&event=push)](https://github.com/facile-it/php-codec/actions/workflows/ci.yaml)
[![Static analysis](https://github.com/facile-it/php-codec/actions/workflows/static-analysis.yaml/badge.svg?branch=master&event=push)](https://github.com/facile-it/php-codec/actions/workflows/static-analysis.yaml)
[![codecov](https://codecov.io/gh/facile-it/php-codec/branch/master/graph/badge.svg?token=HP4OFEEPY6)](https://codecov.io/gh/facile-it/php-codec) [![Join the chat at https://gitter.im/php-codec/community](https://badges.gitter.im/php-codec/community.svg)](https://gitter.im/php-codec/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Install it now. It only requires PHP >= 7.4.
    
    composer require facile-it/php-codec

# Disclaimer

This project is under active development: it's unstable and still poorly documented.
The API is likely to change several times, and it won't be ready for production soon.

The project follows [semantic versioning](https://semver.org/).

# Introduction

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
> A value of type `Type<A, O, I>` (called "codec") is the run time representation of the static type `A`.

# Getting started

    composer require facile-it/php-codec

## Decoders

Decoders are objects with decoding capabilities.
A decoder of type `Decoder<I, A>` takes an input of type `I` and builds a result of type `Validation<A>`.

The class `Facile\PhpCodec\Decoders` provides factory methods for built-in decoders and combinators.

### How to use decoders

```php
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;

/** @var Decoder<string, int> $decoder */
$decoder = Decoders::intFromString();

/** @var Validation<int> $v1 */
$v1 = $decoder->decode('123');
// Since '123' is a numeric string which represents an integer,
// then we can expect the decoding to be successful.
// Hence, $v1 will be an instance of ValidationSuccess

if($v1 instanceof ValidationSuccess) {
    var_dump($v1->getValue());
}

/** @var Validation<int> $v2 */
$v2 = $decoder->decode('hello');
// Similarly, since 'hello' is not a numeric string, we expect 
// the decoding fail. $v2 will be an instance of ValidationError

if($v2 instanceof ValidationFailures) {
    var_dump($v2->getErrors());
}
```

### Dealing with the validation result

We can use `Validation::fold` to destruct the validation result while providing 
a valid result in any case. 

```php
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Validation\Validation;

/** @var Decoder<string, int> $decoder */
$decoder = Decoders::intFromString();

Validation::fold(
    function (\Facile\PhpCodec\Validation\ValidationFailures $failures): int {
        // I may not care about the error.
        // Here I want to give a default value when the deconding fails.
        return 0;
    },
    function (\Facile\PhpCodec\Validation\ValidationSuccess $success): int {
        return $success->getValue();
    },
    $decoder->decode($input)
);
```

You can use the path reporter to build well-formatted error messages for failures.

```php
use Facile\PhpCodec\Decoders;

$decoder = Decoders::intFromString();
$v = $decoder->decode('hello');
$msgs = \Facile\PhpCodec\Reporters::path()->report($v);

var_dump($msgs);
/* This will print 
array(1) {
  [0] =>
  string(49) "Invalid value "hello" supplied to : IntFromString"
}
*/
```

## Examples

Take a look to the [examples](https://github.com/facile-it/php-codec/tree/master/tests/examples) folder.


# Reporters

Reporters do create reports from `Validation` objects.
Generally speaking, reporters are objects that implement the `Reporter<T>` interface, given `T` the type of the report that the generate.

One interesting group of reporters is the validation error reporters group.
They implements `Reporter<list<string>>`.
Thus, given a `Validation` object, they generate a list of error messages for each validation error.

PHP-Codec comes with two error reporters:

- PathReporter, which is a pretty straightforward porting of io-ts' [PathReporter](https://github.com/gcanti/io-ts/blob/master/index.md#error-reporters).
- SimplePathReporter, which is a simplified (read: shorter messages) version of the PathReporter.

```php
$d = Decoders::arrayProps([
  'a' => Decoders::arrayProps([
    'a1' => Decoders::int(),
    'a2' => Decoders::string(),
  ]),
  'b' => Decoders::arrayProps(['b1' => Decoders::bool()])
]);
$v = $d->decode(['a'=> ['a1' => 'str', 'a2' => 1], 'b' => 2]);

$x = \Facile\PhpCodec\Reporters::path()->report($v);
// $x will be
// ['Invalid value "str" supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/a: {a1: int, a2: string}/a1: int',
//  'Invalid value 1 supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/a: {a1: int, a2: string}/a2: string',
//  'Invalid value undefined supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/b: {b1: bool}/b1: bool']

$y = \Facile\PhpCodec\Reporters::simplePath()->report($v);
// $y will be
// ['/a/a1: Invalid value "str" supplied to decoder "int"',
//  '/a/a2: Invalid value 1 supplied to decoder "string"',
//  '/b/b1: Invalid value undefined supplied to decoder "bool"']
```
