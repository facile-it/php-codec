<?php declare(strict_types=1);

namespace Pybatt\Codec;

use Pybatt\Codec\Internal\Arrays\ListType;
use Pybatt\Codec\Internal\Arrays\MapType;
use Pybatt\Codec\Internal\Combinators\ClassFromArray;
use Pybatt\Codec\Internal\Combinators\ComposeType;
use Pybatt\Codec\Internal\Combinators\UnionType;
use Pybatt\Codec\Internal\Primitives\BoolType;
use Pybatt\Codec\Internal\Primitives\FloatType;
use Pybatt\Codec\Internal\Primitives\IntType;
use Pybatt\Codec\Internal\Primitives\NullType;
use Pybatt\Codec\Internal\Primitives\StringType;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Internal\Useful\IntFromStringType;

final class Codecs
{
    /**
     * @return Type<null, mixed, null>
     */
    public static function null(): Type
    {
        return new NullType();
    }

    /**
     * @return Type<string, mixed, string>
     */
    public static function string(): Type
    {
        return new StringType();
    }

    /**
     * @return Type<int, mixed, int>
     */
    public static function int(): Type
    {
        return new IntType();
    }

    /**
     * @return Type<float, mixed, float>
     */
    public static function float(): Type
    {
        return new FloatType();
    }

    /**
     * @return Type<bool, mixed, bool>
     */
    public static function bool(): Type
    {
        return new BoolType();
    }

    /**
     * @return Type<int, string, int>
     */
    public static function intFromString(): Type
    {
        return new IntFromStringType();
    }

    /**
     * @template T
     * @param Type<T,mixed,T> $itemCodec
     * @return ListType<T>
     */
    public static function listt(Type $itemCodec): ListType
    {
        return new ListType($itemCodec);
    }

    /**
     * @template T
     * @param non-empty-array<string, Type> $props
     * @param callable(...mixed):T $factory
     * @param class-string<T> $fqcn
     * @return Type<T, mixed, T>
     */
    public static function classFromArray(
        array $props,
        callable $factory,
        string $fqcn
    ): Type
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
     * @param Type<A, IA, mixed> $a
     * @param Type<B, A, OB> $b
     * @param Type<C, B, OC> | null $c
     * @param Type<D, C, OD> | null $d
     * @param Type<E, D, OE> | null $e
     *
     * // TODO must add type assertions
     * @return (func_num_args() is 2 ? Type<B, IA, OB>
     *   : (func_num_args() is 3 ? Type<C, IA, OC>
     *   : (func_num_args() is 4 ? Type<D, IA, OD>
     *   : (func_num_args() is 5 ? Type<E, IA, OC> : Type)
     * )))
     */
    public static function pipe(
        Type $a,
        Type $b,
        ?Type $c = null,
        ?Type $d = null,
        ?Type $e = null
    ): Type
    {
        // Order is important: composition is not commutative
        return new ComposeType(
            $a,
            $c instanceof Type
                ? self::pipe($b, $c, $d, $e)
                : $b
        );
    }

    /**
     * @param Type $a
     * @param Type $b
     * @param Type ...$others
     * @return UnionType
     *
     * TODO simple to write, awful to type
     */
    public static function union(Type $a, Type $b, Type ...$others): UnionType
    {
        // Order is not important, unions should be commutatives
        return array_reduce(
            $others,
            static function (Type $carry, Type $current): Type {
                return new UnionType($current, $carry);
            },
            new UnionType($a, $b)
        );
    }
}
