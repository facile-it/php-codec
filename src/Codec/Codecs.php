<?php declare(strict_types=1);

namespace Pybatt\Codec;

use Pybatt\Codec\Arrays\ArrayType;
use Pybatt\Codec\CommonTypes\ClassFromArray;
use Pybatt\Codec\CommonTypes\UnionType;
use Pybatt\Codec\Primitives\FloatType;
use Pybatt\Codec\Primitives\IntType;
use Pybatt\Codec\Primitives\NullType;
use Pybatt\Codec\Primitives\StringType;

final class Codecs
{
    public static function null(): NullType
    {
        return new NullType();
    }

    public static function string(): StringType
    {
        return new StringType();
    }

    public static function int(): IntType
    {
        return new IntType();
    }

    public static function float(): FloatType
    {
        return new FloatType();
    }

    /**
     * @template T
     * @param Type<T,mixed,T> $itemCodec
     * @return ArrayType<T>
     */
    public static function listt(Type $itemCodec): ArrayType
    {
        return new ArrayType($itemCodec);
    }

    /**
     * @template T
     * @param non-empty-array<string, Type> $props
     * @param callable(...mixed):T $factory
     * @return ClassFromArray<T>
     */
    public static function classFromArray(array $props, callable $factory): ClassFromArray
    {
        return new ClassFromArray($props, $factory);
    }

    /**
     * @param Type $a
     * @param Type $b
     * @param Type ...$others
     * @return UnionType
     *
     * TODO semplice da scrivere, ma perde completamente la tipizzazione
     */
    public static function unionType(Type $a, Type $b, Type ...$others): UnionType
    {
        return array_reduce(
            $others,
            static function (Type $carry, Type $current): Type {
                return new UnionType($current, $carry);
            },
            new UnionType($a, $b)
        );
    }
}
