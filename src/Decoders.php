<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Primitives\CallableDecoder;
use Facile\PhpCodec\Internal\Primitives\IntDecoder;
use Facile\PhpCodec\Internal\Primitives\MixedDecoder;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Internal\Useful\IntFromStringDecoder;
use Facile\PhpCodec\Utils\ConcreteDecoder;
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
     * @psalm-param Decoder<I, A> $da
     * @psalm-return Decoder<I, B>
     */
    public static function map(callable $f, Decoder $da): Decoder
    {
        return new ConcreteDecoder(
            /**
             * @param I $i
             */
            function ($i, Context $context) use ($f, $da): Validation {
                return Validation::map($f, $da->validate($i, $context));
            },
            $da->getName()
        );
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

    public static function int(): Decoder
    {
        return new IntDecoder();
    }

    /**
     * @psalm-return Decoder<string, int>
     */
    public static function intFromString(): Decoder
    {
        return new IntFromStringDecoder();
    }

    /**
     * @psalm-return Decoder<mixed, mixed>
     */
    public static function mixed(): Decoder
    {
        return new MixedDecoder();
    }

    /**
     * @psalm-return Decoder<mixed, callable>
     */
    public static function callable(): Decoder
    {
        return new CallableDecoder();
    }
}
