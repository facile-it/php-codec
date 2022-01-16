<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Arrays\ListOfDecoder;
use Facile\PhpCodec\Internal\Combinators\ArrayPropsDecoder;
use Facile\PhpCodec\Internal\Combinators\ComposeDecoder;
use Facile\PhpCodec\Internal\Combinators\IntersectionDecoder;
use Facile\PhpCodec\Internal\Combinators\LiteralDecoder;
use Facile\PhpCodec\Internal\Combinators\MapDecoder;
use Facile\PhpCodec\Internal\Combinators\UnionDecoder;
use Facile\PhpCodec\Internal\Primitives\BoolDecoder;
use Facile\PhpCodec\Internal\Primitives\CallableDecoder;
use Facile\PhpCodec\Internal\Primitives\FloatDecoder;
use Facile\PhpCodec\Internal\Primitives\IntDecoder;
use Facile\PhpCodec\Internal\Primitives\MixedDecoder;
use Facile\PhpCodec\Internal\Primitives\NullDecoder;
use Facile\PhpCodec\Internal\Primitives\StringDecoder;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Internal\Useful\DateTimeFromStringDecoder;
use Facile\PhpCodec\Internal\Useful\IntFromStringDecoder;
use Facile\PhpCodec\Internal\Useful\RegexDecoder;
use Facile\PhpCodec\Internal\Useful\StringMatchingRegexDecoder;
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
     * @psalm-param callable(I, Context):Validation<A> $f
     * @psalm-param string $name
     * @psalm-return Decoder<I, A>
     */
    public static function make(callable $f, string $name = 'anon'): Decoder
    {
        return new ConcreteDecoder($f, $name);
    }

    /**
     * @template I
     * @template A
     * @template B
     * @psalm-param Decoder<A, B> $db
     * @psalm-param Decoder<I, A> $da
     * @psalm-return Decoder<I, B>
     */
    public static function compose(Decoder $db, Decoder $da): Decoder
    {
        return new ComposeDecoder($db, $da);
    }

    /**
     * @psalm-template IA
     * @psalm-template A
     * @psalm-template B
     * @psalm-template C
     * @psalm-template D
     * @psalm-template E
     *
     * @psalm-param Decoder<IA, A> $a
     * @psalm-param Decoder<A, B> $b
     * @psalm-param Decoder<B, C> | null $c
     * @psalm-param Decoder<C, D> | null $d
     * @psalm-param Decoder<D, E> | null $e
     *
     * @psalm-return (func_num_args() is 2 ? Decoder<IA, B>
     *                          : (func_num_args() is 3 ? Decoder<IA, C>
     *                          : (func_num_args() is 4 ? Decoder<IA, D>
     *                          : (func_num_args() is 5 ? Decoder<IA, E> : Decoder)
     *                          )))
     */
    public static function pipe(
        Decoder $a,
        Decoder $b,
        ?Decoder $c = null,
        ?Decoder $d = null,
        ?Decoder $e = null
    ): Decoder {
        // Order is important: composition is not commutative
        return self::compose(
            $c instanceof Decoder
                ? self::pipe($b, $c, $d, $e)
                : $b,
            $a
        );
    }

    /**
     * @psalm-template IA
     * @psalm-template A
     * @psalm-template IB
     * @psalm-template B
     * @psalm-template IC
     * @psalm-template C
     * @psalm-template ID
     * @psalm-template D
     * @psalm-template IE
     * @psalm-template E
     *
     * @psalm-param Decoder<IA, A> $a
     * @psalm-param Decoder<IB, B> $b
     * @psalm-param Decoder<IC, C> | null $c
     * @psalm-param Decoder<ID, D> | null $d
     * @psalm-param Decoder<IE, E> | null $e
     *
     * @psalm-return (func_num_args() is 2 ? Decoder<IA & IB, A | B>
     *                          : (func_num_args() is 3 ? Decoder<IA & IB & IC, A | B | C>
     *                          : (func_num_args() is 4 ? Decoder<IA & IB & IC & ID, A | B | C | D>
     *                          : (func_num_args() is 5 ? Decoder<IA & IB & IC & ID & IE, A | B | C | D | E> : Decoder)
     *                          )))
     */
    public static function union(Decoder $a, Decoder $b, ?Decoder $c = null, ?Decoder $d = null, ?Decoder $e = null): Decoder
    {
        // Order is important, this is not commutative

        $args = array_values(
            array_filter(
                func_get_args(),
                static function ($x): bool {
                    return $x instanceof Decoder;
                }
            )
        );
        $argc = count($args);

        $res = new UnionDecoder($args[$argc - 2], $args[$argc - 1], $argc - 2);

        for ($i = $argc - 3; $i >= 0; --$i) {
            $res = new UnionDecoder(
                $args[$i],
                $res,
                $i
            );
        }

        return $res;
    }

    /**
     * @psalm-template I
     * @psalm-template A
     * @psalm-template B
     *
     * @psalm-param Decoder<I, A> $a
     * @psalm-param Decoder<I, B> $b
     * Intersection works only for objects and object-like arrays :(
     * @psalm-return (A is object ? ( B is object ? Decoder<I, A & B> : Decoder<I, A | B>) : Decoder<I, A | B>)
     */
    public static function intersection(Decoder $a, Decoder $b): Decoder
    {
        // Intersection seems to mess up implements annotation
        /** @var Decoder<I, A & B> */
        return new IntersectionDecoder($a, $b);
    }

    /**
     * This is structurally equivalent to a map function
     * map :: (a -> b) -> Decoder a -> Decoder b
     *
     * I still don't know if decoders could be functors or something more complicated.
     * By now, let me introduce it with this strange name. I just need this feature.
     *
     * @template I
     * @template A
     * @template B
     * @psalm-param callable(A):B $f
     * @psalm-param Decoder<I, A> $da
     * @psalm-return Decoder<I, B>
     */
    public static function transformValidationSuccess(callable $f, Decoder $da): Decoder
    {
        return self::compose(
            new MapDecoder($f, $da->getName()),
            $da
        );
    }

    /**
     * @psalm-template T of bool | string | int
     * @psalm-param T $l
     * @psalm-return Decoder<mixed, T>
     *
     * @param mixed $l
     */
    public static function literal($l): Decoder
    {
        return new LiteralDecoder($l);
    }

    /**
     * @psalm-template I
     * @psalm-template T
     *
     * @param Decoder<I, T> $elementDecoder
     *
     * @return Decoder<mixed, list<T>>
     */
    public static function listOf(Decoder $elementDecoder): Decoder
    {
        return new ListOfDecoder($elementDecoder);
    }

    /**
     * @psalm-template K of array-key
     * @psalm-template Vs
     * @psalm-template PD of non-empty-array<K, Decoder<mixed, Vs>>
     *
     * @psalm-param PD $props
     * @psalm-return Decoder<mixed, non-empty-array<K, Vs>>
     *
     * @param Decoder[] $props
     *
     * @return Decoder
     *
     * Waiting for this feature to provide a better typing. I need something like mapped types from Typescript.
     *
     * @see https://github.com/vimeo/psalm/issues/3589
     */
    public static function arrayProps(array $props): Decoder
    {
        return new ArrayPropsDecoder($props);
    }

    /**
     * @psalm-template T of object
     * @psalm-template K of array-key
     * @psalm-template Vs
     * @psalm-template PD of non-empty-array<K, Vs>
     * @psalm-template CF of callable(...mixed):T
     * @psalm-param Decoder<mixed, PD> $propsDecoder
     * @psalm-param CF $factory
     * @psalm-param string $decoderName
     * @psalm-return Decoder<mixed, T>
     */
    public static function classFromArrayPropsDecoder(
        Decoder $propsDecoder,
        callable $factory,
        string $decoderName
    ): Decoder {
        return self::pipe(
            $propsDecoder,
            new MapDecoder(
                function (array $props) use ($factory) {
                    return destructureIn($factory)(\array_values($props));
                },
                \sprintf('%s(%s)', $decoderName, $propsDecoder->getName())
            )
        );
    }

    ############################################################
    #
    # Primitives
    #
    ############################################################

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

    /**
     * @return Decoder<mixed, null>
     */
    public static function null(): Decoder
    {
        return new NullDecoder();
    }

    /**
     * @psalm-return Decoder<mixed, bool>
     */
    public static function bool(): Decoder
    {
        return new BoolDecoder();
    }

    /**
     * @psalm-return Decoder<mixed, int>
     */
    public static function int(): Decoder
    {
        return new IntDecoder();
    }

    /**
     * @psalm-return Decoder<mixed, float>
     */
    public static function float(): Decoder
    {
        return new FloatDecoder();
    }

    /**
     * @psalm-return Decoder<mixed, string>
     */
    public static function string(): Decoder
    {
        return new StringDecoder();
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

    ############################################################
    #
    # Useful decoders
    #
    ############################################################

    /**
     * @psalm-return Decoder<string, int>
     */
    public static function intFromString(): Decoder
    {
        return new IntFromStringDecoder();
    }

    /**
     * @psalm-return Decoder<string, \DateTimeInterface>
     */
    public static function dateTimeFromString(string $format = \DATE_ATOM): Decoder
    {
        return new DateTimeFromStringDecoder($format);
    }

    /**
     * @psalm-return Decoder<string, string[]>
     */
    public static function regex(string $regex): Decoder
    {
        return new RegexDecoder($regex);
    }

    /**
     * @psalm-param string $regex
     * @psalm-return Decoder<string, string>
     */
    public static function stringMatchingRegex(string $regex): Decoder
    {
        return new StringMatchingRegexDecoder($regex);
    }
}
