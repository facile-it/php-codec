<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Arrays\ListCodec;
use Facile\PhpCodec\Internal\Arrays\MapType;
use Facile\PhpCodec\Internal\Combinators\ClassFromArray;
use Facile\PhpCodec\Internal\Combinators\ComposeCodec;
use Facile\PhpCodec\Internal\Combinators\LiteralType;
use Facile\PhpCodec\Internal\Combinators\UnionCodec;
use Facile\PhpCodec\Internal\IdentityEncoder;
use Facile\PhpCodec\Internal\Primitives\BoolType;
use Facile\PhpCodec\Internal\Primitives\CallableDecoder;
use Facile\PhpCodec\Internal\Primitives\FloatType;
use Facile\PhpCodec\Internal\Primitives\IntDecoder;
use Facile\PhpCodec\Internal\Primitives\MixedDecoder;
use Facile\PhpCodec\Internal\Primitives\NullType;
use Facile\PhpCodec\Internal\Primitives\StringType;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Internal\Useful\DateTimeFromIsoStringType;
use Facile\PhpCodec\Internal\Useful\IntFromStringDecoder;
use Facile\PhpCodec\Internal\Useful\RegexType;
use Facile\PhpCodec\Utils\ConcreteCodec;

final class Codecs
{
    /**
     * @psalm-return Codec<null, mixed, null>
     */
    public static function null(): Codec
    {
        return new NullType();
    }

    /**
     * @psalm-return Codec<string, mixed, string>
     */
    public static function string(): Codec
    {
        return new StringType();
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
        return self::fromDecoder(new IntDecoder());
    }

    /**
     * @psalm-return Codec<float, mixed, float>
     */
    public static function float(): Codec
    {
        return new FloatType();
    }

    /**
     * @psalm-return Codec<bool, mixed, bool>
     */
    public static function bool(): Codec
    {
        return new BoolType();
    }

    /**
     * @psalm-template T of bool | string | int
     * @psalm-param T $x
     * @psalm-return Codec<T, mixed, T>
     *
     * @param mixed $x
     */
    public static function literal($x): Codec
    {
        return new LiteralType($x);
    }

    /**
     * @psalm-return Codec<int, string, int>
     *
     * @deprecated use decoder instead
     * @see Decoders::intFromString()
     */
    public static function intFromString(): Codec
    {
        return self::fromDecoder(new IntFromStringDecoder());
    }

    /**
     * @psalm-return Codec<\DateTime, string, \DateTime>
     */
    public static function dateTimeFromIsoString(): Codec
    {
        return new DateTimeFromIsoStringType();
    }

    /**
     * @psalm-template T
     * @psalm-param Codec<T, mixed, T> $itemCodec
     * @psalm-return Codec<list<T>, mixed, list<T>>
     */
    public static function listt(Codec $itemCodec): Codec
    {
        return new ListCodec($itemCodec);
    }

    /**
     * @psalm-template T
     * @psalm-param non-empty-array<string, Codec> $props
     * @psalm-param callable(...mixed):T           $factory
     * @psalm-param class-string<T>                $fqcn
     * @psalm-return Codec<T, mixed, T>
     */
    public static function classFromArray(
        array $props,
        callable $factory,
        string $fqcn
    ): Codec {
        return self::pipe(
            new MapType(),
            new ClassFromArray($props, $factory, $fqcn)
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
     *                          : (func_num_args() is 5 ? Codec<E, IA, OC> : Codec)
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
        return new ComposeCodec(
            $a,
            $c instanceof Codec
                ? self::pipe($b, $c, $d, $e)
                : $b
        );
    }

    public static function union(Codec $a, Codec $b, Codec ...$others): Codec
    {
        // Order is important, this is not commutative
        return \array_reduce(
            $others,
            static function (Codec $carry, Codec $current): Codec {
                return new UnionCodec($current, $carry);
            },
            new UnionCodec($a, $b)
        );
    }

    /**
     * @psalm-return Codec<string[], string, string[]>
     */
    public static function regex(string $regex): Codec
    {
        return new RegexType($regex);
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
        return self::fromDecoder(new MixedDecoder());
    }

    /**
     * @psalm-return Codec<callable, mixed, callable>
     *
     * @deprecated use decoder instead
     * @see Decoders::callable()
     */
    public static function callable(): Codec
    {
        return self::fromDecoder(new CallableDecoder());
    }
}
