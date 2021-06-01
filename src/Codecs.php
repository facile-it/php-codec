<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Combinators\PipeCodec;
use Facile\PhpCodec\Internal\IdentityEncoder;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Utils\ConcreteCodec;

final class Codecs
{
    /**
     * @psalm-return Codec<null, mixed, null>
     *
     * @deprecated use decoder instead
     * @see Decoders::null()
     */
    public static function null(): Codec
    {
        return self::fromDecoder(Decoders::null());
    }

    /**
     * @psalm-return Codec<string, mixed, string>
     *
     * @deprecated use decoder instead
     * @see Decoders::string()
     */
    public static function string(): Codec
    {
        return self::fromDecoder(Decoders::string());
    }

    /**
     * @template I of mixed
     * @psalm-return Codec<int, I, int>
     *
     * @deprecated use decoder instead
     * @see Decoders::int()
     */
    public static function int(): Codec
    {
        return self::fromDecoder(Decoders::int());
    }

    /**
     * @psalm-return Codec<float, mixed, float>
     *
     * @deprecated use decoder instead
     * @see Decoders::float()
     */
    public static function float(): Codec
    {
        return self::fromDecoder(Decoders::float());
    }

    /**
     * @psalm-return Codec<bool, mixed, bool>
     *
     * @deprecated use decoder instead
     * @see Decoders::bool()
     */
    public static function bool(): Codec
    {
        return self::fromDecoder(Decoders::bool());
    }

    /**
     * @psalm-template T of bool | string | int
     * @psalm-param T $x
     * @psalm-return Codec<T, mixed, T>
     *
     * @param mixed $x
     *
     * @deprecated use decoder instead
     * @see Decoders::literal()
     */
    public static function literal($x): Codec
    {
        return self::fromDecoder(Decoders::literal($x));
    }

    /**
     * @psalm-return Codec<int, string, int>
     *
     * @deprecated use decoder instead
     * @see Decoders::intFromString()
     */
    public static function intFromString(): Codec
    {
        return self::fromDecoder(Decoders::intFromString());
    }

    /**
     * @psalm-return Codec<\DateTimeInterface, string, \DateTimeInterface>
     *
     * @deprecated use decoder instead
     * @see Decoders::dateTimeFromString()
     */
    public static function dateTimeFromIsoString(): Codec
    {
        return self::fromDecoder(Decoders::dateTimeFromString());
    }

    /**
     * @psalm-template T
     * @psalm-param Codec<T, mixed, T> $itemCodec
     * @psalm-return Codec<list<T>, mixed, list<T>>
     *
     * @deprecated use decoder instead
     * @see Decoders::listOf()
     */
    public static function listt(Codec $itemCodec): Codec
    {
        return self::fromDecoder(Decoders::listOf($itemCodec));
    }

    /**
     * @psalm-template T of object
     * @psalm-template K of array-key
     * @psalm-template Vs
     * @psalm-template PD of non-empty-array<K, Decoder<mixed, Vs>>
     * @psalm-param PD $props
     * @psalm-param callable(...Vs):T           $factory
     * @psalm-param class-string<T>                $fqcn
     * @psalm-return Codec<T, mixed, T>
     *
     * @deprecated use decoder instead
     * @see Decoders::classFromArrayPropsDecoder()
     */
    public static function classFromArray(
        array $props,
        callable $factory,
        string $fqcn
    ): Codec {
        /** @var Decoder<mixed, non-empty-array<array-key, Vs>> $propsDecoder */
        $propsDecoder = Decoders::arrayProps($props);

        return self::fromDecoder(
            Decoders::classFromArrayPropsDecoder(
                $propsDecoder,
                $factory,
                $fqcn
            )
        );
    }

    /**
     * @psalm-template A
     * @psalm-template IA
     * @psalm-template B
     * @psalm-template OB
     * @psalm-template C
     * @psalm-template OC
     * @psalm-template D
     * @psalm-template OD
     * @psalm-template E
     * @psalm-template OE
     *
     * @psalm-param Codec<A, IA, mixed>    $a
     * @psalm-param Codec<B, A, OB>        $b
     * @psalm-param Codec<C, B, OC> | null $c
     * @psalm-param Codec<D, C, OD> | null $d
     * @psalm-param Codec<E, D, OE> | null $e
     *
     * @psalm-return (func_num_args() is 2 ? Codec<B, IA, OB>
     *                          : (func_num_args() is 3 ? Codec<C, IA, OC>
     *                          : (func_num_args() is 4 ? Codec<D, IA, OD>
     *                          : (func_num_args() is 5 ? Codec<E, IA, OE> : Codec)
     *                          )))
     */
    public static function pipe(
        Codec $a,
        Codec $b,
        ?Codec $c = null,
        ?Codec $d = null,
        ?Codec $e = null
    ): Codec {
        // Order is important: composition is not commutative
        return new PipeCodec(
            $a,
            $c instanceof Codec
                ? self::pipe($b, $c, $d, $e)
                : $b
        );
    }

    /**
     * @param Codec $a
     * @param Codec $b
     * @param Codec ...$others
     *
     * @return Codec
     *
     * @deprecated use decoder instead
     * @see Decoders::union()
     */
    public static function union(Codec $a, Codec $b, Codec ...$others): Codec
    {
        // Order is important, this is not commutative
        return \array_reduce(
            $others,
            static function (Codec $carry, Codec $current): Codec {
                return self::fromDecoder(Decoders::union($current, $carry));
            },
            self::fromDecoder(Decoders::union($a, $b))
        );
    }

    /**
     * @psalm-return Codec<string[], string, string[]>
     *
     * @deprecated use decoder instead
     * @see Decoders::regex()
     */
    public static function regex(string $regex): Codec
    {
        return self::fromDecoder(Decoders::regex($regex));
    }

    /**
     * @psalm-template I
     * @psalm-template T
     *
     * @psalm-param Decoder<I, T> $decoder
     * @psalm-return Codec<T, I, T>
     */
    public static function fromDecoder(Decoder $decoder): Codec
    {
        return new ConcreteCodec($decoder, new IdentityEncoder());
    }

    /**
     * @psalm-template U
     *
     * @psalm-param U | null $default
     * @psalm-return Codec<U, mixed, U>
     *
     * @param null|mixed $default
     *
     * @deprecated use decoder instead
     * @see Decoders::undefined()
     */
    public static function undefined($default = null): Codec
    {
        return self::fromDecoder(new UndefinedDecoder($default));
    }

    /**
     * @psalm-template U of mixed
     * @psalm-return Codec<U, mixed, U>
     *
     * @deprecated use decoder instead
     * @see Decoders::mixed()
     */
    public static function mixed(): Codec
    {
        return self::fromDecoder(Decoders::mixed());
    }

    /**
     * @psalm-return Codec<callable, mixed, callable>
     *
     * @deprecated use decoder instead
     * @see Decoders::callable()
     */
    public static function callable(): Codec
    {
        return self::fromDecoder(Decoders::callable());
    }
}
