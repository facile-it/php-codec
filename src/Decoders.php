<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

final class Decoders
{
    private function __construct()
    {
    }

    /**
     * @template I
     * @template A
     * @template B
     * @psalm-param callable(A):B $f
     * @psalm-return callable(Decoder<I, A>): Decoder<I, B>
     *
     * @param callable $f
     *
     * @return callable
     */
    public static function map(callable $f): callable
    {
        return function (Decoder $da) use ($f): Decoder {
            return new ConcreteDecoder(
                /**
                 * @param I $i
                 */
                function ($i, Context $context) use ($f, $da): Validation {
                    return Validation::map($f, $da->validate($i, $context));
                },
                $da->getName()
            );
        };
    }

    /**
     * @psalm-template U
     * @psalm-param U $default
     * @psalm-return Decoder<mixed, U>
     *
     * @param null|mixed $default
     */
    public static function undefined($default = null): Decoder
    {
        return new UndefinedDecoder($default);
    }
}
