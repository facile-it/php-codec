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
use Facile\PhpCodec\Internal\Primitives\IntType;
use Facile\PhpCodec\Internal\Primitives\MixedDecoder;
use Facile\PhpCodec\Internal\Primitives\NullType;
use Facile\PhpCodec\Internal\Primitives\StringType;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Internal\Useful\DateTimeFromIsoStringType;
use Facile\PhpCodec\Internal\Useful\IntFromStringType;
use Facile\PhpCodec\Internal\Useful\RegexType;

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
     * @psalm-return Codec<int, mixed, int>
     */
    public static function int(): Codec
    {
        return new IntType();
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
     * @template T of bool | string | int
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
     */
    public static function intFromString(): Codec
    {
        return new IntFromStringType();
    }

    /**
     * @psalm-return Codec<\DateTime, string, \DateTime>
     */
    public static function dateTimeFromIsoString(): Codec
    {
        return new DateTimeFromIsoStringType();
    }

    /**
     * @template T
     * @psalm-param Codec<T, mixed, T> $itemCodec
     * @psalm-return Codec<list<T>, mixed, list<T>>
     */
    public static function listt(Codec $itemCodec): Codec
    {
        return new ListCodec($itemCodec);
    }

    /**
     * @template T
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
     * @template A
     * @template IA
     * @template B
     * @template OB
     * @template C
     * @template OC
     * @template D
     * @template OD
     * @template E
     * @template OE
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
     * @template I
     * @template T
     *
     * @psalm-param Decoder<I, T> $decoder
     * @psalm-return Codec<T, I, T>
     */
    public static function fromDecoder(Decoder $decoder): Codec
    {
        return new ConcreteCodec($decoder, new IdentityEncoder());
    }

    /**
     * @template U
     *
     * @psalm-param U | null $default
     * @psalm-return Codec<U, mixed, U>
     *
     * @param null|mixed $default
     */
    public static function undefined($default = null): Codec
    {
        return self::fromDecoder(new UndefinedDecoder($default));
    }

    /**
     * @template U of mixed
     * @psalm-return Codec<U, mixed, U>
     */
    public static function mixed(): Codec
    {
        return self::fromDecoder(new MixedDecoder());
    }

    /**
     * @psalm-return Codec<callable, mixed, callable>
     */
    public static function callable(): Codec
    {
        return self::fromDecoder(new CallableDecoder());
    }
}
