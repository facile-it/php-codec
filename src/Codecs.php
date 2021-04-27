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
     * @return Codec<null, mixed, null>
     */
    public static function null(): Codec
    {
        return new NullType();
    }

    /**
     * @return Codec<string, mixed, string>
     */
    public static function string(): Codec
    {
        return new StringType();
    }

    /**
     * @return Codec<int, mixed, int>
     */
    public static function int(): Codec
    {
        return new IntType();
    }

    /**
     * @return Codec<float, mixed, float>
     */
    public static function float(): Codec
    {
        return new FloatType();
    }

    /**
     * @return Codec<bool, mixed, bool>
     */
    public static function bool(): Codec
    {
        return new BoolType();
    }

    /**
     * @template T of bool | string | int
     *
     * @param T $x
     *
     * @return Codec<T, mixed, T>
     */
    public static function literal($x): Codec
    {
        return new LiteralType($x);
    }

    /**
     * @return Codec<int, string, int>
     */
    public static function intFromString(): Codec
    {
        return new IntFromStringType();
    }

    /**
     * @return Codec<\DateTime, string, \DateTime>
     */
    public static function dateTimeFromIsoString(): Codec
    {
        return new DateTimeFromIsoStringType();
    }

    /**
     * @template T
     *
     * @param Codec<T,mixed,T> $itemCodec
     *
     * @return Codec<list<T>, mixed, list<T>>
     */
    public static function listt(Codec $itemCodec): Codec
    {
        return new ListCodec($itemCodec);
    }

    /**
     * @template T
     *
     * @param non-empty-array<string, Codec> $props
     * @param callable(...mixed):T           $factory
     * @param class-string<T>                $fqcn
     *
     * @return Codec<T, mixed, T>
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
     * @param Codec<A, IA, mixed>    $a
     * @param Codec<B, A, OB>        $b
     * @param Codec<C, B, OC> | null $c
     * @param Codec<D, C, OD> | null $d
     * @param Codec<E, D, OE> | null $e
     *
     * // TODO must add type assertions
     *
     * @return (func_num_args() is 2 ? Codec<B, IA, OB>
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

    /**
     * @param Codec ...$others
     *
     * @return Codec
     *
     * TODO simple to write, awful to type
     */
    public static function union(Codec $a, Codec $b, Codec ...$others): Codec
    {
        // Order is not important, unions should be commutatives
        return \array_reduce(
            $others,
            static function (Codec $carry, Codec $current): Codec {
                return new UnionCodec($current, $carry);
            },
            new UnionCodec($a, $b)
        );
    }

    /**
     * @return Codec<string[], string, string[]>
     */
    public static function regex(string $regex): Codec
    {
        return new RegexType($regex);
    }

    /**
     * @template I
     * @template T
     *
     * @param Decoder<I, T> $decoder
     *
     * @return Codec<T, I, T>
     */
    public static function fromDecoder(Decoder $decoder): Codec
    {
        return new ConcreteCodec($decoder, new IdentityEncoder());
    }

    /**
     * @template U
     *
     * @param U | null $default
     *
     * @return Codec<U, mixed, U>
     */
    public static function undefined($default = null): Codec
    {
        return self::fromDecoder(new UndefinedDecoder($default));
    }

    /**
     * @template U of mixed
     *
     * @return Codec<U, U, U>
     */
    public static function mixed(): Codec
    {
        return self::fromDecoder(new MixedDecoder());
    }

    /**
     * @return Codec<callable, mixed, callable>
     */
    public static function callable(): Codec
    {
        return self::fromDecoder(new CallableDecoder());
    }
}
