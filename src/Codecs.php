<?php declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Arrays\ListOfType;
use Facile\PhpCodec\Internal\Arrays\MapType;
use Facile\PhpCodec\Internal\Combinators\ClassFromArray;
use Facile\PhpCodec\Internal\Combinators\ComposeType;
use Facile\PhpCodec\Internal\Combinators\UnionType;
use Facile\PhpCodec\Internal\Experimental\AssociativeArrayType;
use Facile\PhpCodec\Internal\Primitives\BoolType;
use Facile\PhpCodec\Internal\Primitives\FloatType;
use Facile\PhpCodec\Internal\Primitives\IntType;
use Facile\PhpCodec\Internal\Primitives\LitteralType;
use Facile\PhpCodec\Internal\Primitives\NullType;
use Facile\PhpCodec\Internal\Primitives\StringType;
use Facile\PhpCodec\Internal\Primitives\UndefinedType;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Internal\Useful\DateTimeFromIsoStringType;
use Facile\PhpCodec\Internal\Useful\IntFromStringType;
use Facile\PhpCodec\Internal\Useful\RegexType;
use Facile\PhpCodec\Validation\Validation;

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
     * @template A
     * @template I
     * @template O
     * @template T
     *
     * @param Codec<A, I, O> $codec
     * @param T | null $default
     *
     * @return
     */
    public static function undefinableOf(Codec $codec, $default = null): Codec
    {
        return Validation::map(
            function ($decoded) use ($default) {
                return $decoded instanceof Undefined ? $default : $decoded;
            },
            self::union(self::undefined(), $codec)
        );
    }

    /**
     * @template I
     * @template O
     * @template A
     * @template B
     *
     * @param callable(A):B $f
     * @param callable(B):A $g
     * @param Codec<A, I, O> $fa
     * @return Codec<B, I, O>
     */
    public static function invmap(callable $f, callable $g, Codec $fa): Codec
    {
        // TODO I need a compositor codec to store these mappings
    }

    /**
     * @template T of bool | string | int
     * @param T $x
     * @return Codec<T, mixed, T>
     */
    public static function litteral($x): Codec
    {
        return new LitteralType($x);
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
     * @param Codec<T,mixed,T> $itemCodec
     * @return Codec<list<T>, mixed, list<T>>
     */
    public static function listOf(Codec $itemCodec): Codec
    {
        return new ListOfType($itemCodec);
    }

    /**
     * @param non-empty-array<string, Type> $props
     * @return Codec<array, mixed, array>
     */
    public static function associativeArray(array $props):Codec {
        return new AssociativeArrayType($props);
    }

    /**
     * @template T
     * @param non-empty-array<string, Codec> $props
     * @param callable(...mixed):T $factory
     * @param class-string<T> $fqcn
     * @return Codec<T, mixed, T>
     */
    public static function classFromArray(
        array $props,
        callable $factory,
        string $fqcn
    ): Codec
    {
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
     * @param Codec<A, IA, mixed> $a
     * @param Codec<B, A, OB> $b
     * @param Codec<C, B, OC> | null $c
     * @param Codec<D, C, OD> | null $d
     * @param Codec<E, D, OE> | null $e
     *
     * // TODO must add type assertions
     * @return (func_num_args() is 2 ? Codec<B, IA, OB>
     *   : (func_num_args() is 3 ? Codec<C, IA, OC>
     *   : (func_num_args() is 4 ? Codec<D, IA, OD>
     *   : (func_num_args() is 5 ? Codec<E, IA, OC> : Codec)
     * )))
     */
    public static function pipe(
        Codec $a,
        Codec $b,
        ?Codec $c = null,
        ?Codec $d = null,
        ?Codec $e = null
    ): Codec
    {
        // Order is important: composition is not commutative
        return new ComposeType(
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
     * @return Codec
     *
     * TODO simple to write, awful to type
     */
    public static function union(Codec $a, Codec $b, Codec ...$others): Codec
    {
        // Order is not important, unions should be commutatives
        return array_reduce(
            $others,
            static function (Codec $carry, Codec $current): Codec {
                return new UnionType($current, $carry);
            },
            new UnionType($a, $b)
        );
    }

    /**
     * @param string $regex
     * @return Codec<string[], string, string[]>
     */
    public static function regex(string $regex): Codec
    {
        return new RegexType($regex);
    }

    private static function undefined(): Codec
    {
        return new UndefinedType();
    }
}
