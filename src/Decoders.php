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
    private function __construct() {}

    /**
     * @template I
     * @template A
     *
     * @param callable(I, Context):Validation<A> $f
     *
     * @return Decoder<I, A>
     */
    public static function make(callable $f, string $name = 'anon'): Decoder
    {
        return new ConcreteDecoder($f, $name);
    }

    /**
     * @template I
     * @template A
     * @template B
     * @template C
     * @template D
     * @template E
     *
     * @param Decoder<I, A>        $a
     * @param Decoder<A, B>        $b
     * @param Decoder<B, C> | null $c
     * @param Decoder<C, D> | null $d
     * @param Decoder<D, E> | null $e
     *
     * @return ($c is null ? Decoder<I, B> : ($d is null ? Decoder<I, C> : ($e is null ? Decoder<I, D> : Decoder<I, E>)))
     */
    public static function pipe(
        Decoder $a,
        Decoder $b,
        ?Decoder $c = null,
        ?Decoder $d = null,
        ?Decoder $e = null
    ): Decoder {
        // Order is important: composition is not commutative
        /** @phpstan-ignore return.type */
        return $c instanceof Decoder
            ? self::compose(self::pipe($b, $c, $d, $e), $a)
            : self::compose($b, $a);
    }

    /**
     * @template IA
     * @template IB
     * @template A of IB
     * @template B
     *
     * @psalm-param Decoder<IB, B> $db
     * @psalm-param Decoder<IA, A> $da
     *
     * @return Decoder<IA, B>
     */
    public static function compose(Decoder $db, Decoder $da): Decoder
    {
        return new ComposeDecoder($db, $da);
    }

    /**
     * @template I input type
     * @template A
     * @template B
     * @template C
     * @template D
     * @template E
     *
     * @psalm-param Decoder<I, A> $a
     * @psalm-param Decoder<I, B> $b
     * @psalm-param Decoder<I, C> | null $c
     * @psalm-param Decoder<I, D> | null $d
     * @psalm-param Decoder<I, E> | null $e
     *
     * @return ($c is null ? Decoder<I, A | B> : ($d is null ? Decoder<I, A | B | C> : ($e is null ? Decoder<I, A | B | C | D> : Decoder<I, A | B | C | D | E>)))
     */
    public static function union(Decoder $a, Decoder $b, ?Decoder $c = null, ?Decoder $d = null, ?Decoder $e = null): Decoder
    {
        // Order is important, this is not commutative

        $args = array_values(
            array_filter(
                func_get_args(),
                static fn($x): bool => $x instanceof Decoder
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

        /** @phpstan-ignore return.type */
        return $res;
    }

    /**
     * @template IA
     * @template IB
     * @template A
     * @template B
     *
     * @psalm-param Decoder<IA, A> $a
     * @psalm-param Decoder<IB, B> $b
     *
     * @return Decoder<IA & IB, A & B>
     */
    public static function intersection(Decoder $a, Decoder $b): Decoder
    {
        return new IntersectionDecoder($a, $b);
    }

    /**
     * This is structurally equivalent to a map function
     * map :: (a -> b) -> Decoder a -> Decoder b.
     *
     * I still don't know if decoders could be functors or something more complicated.
     * By now, let me introduce it with this strange name. I just need this feature.
     *
     * @template I
     * @template A
     * @template B
     *
     * @psalm-param callable(A):B $f
     * @psalm-param Decoder<I, A> $da
     *
     * @return Decoder<I, B>
     */
    public static function transformValidationSuccess(callable $f, Decoder $da): Decoder
    {
        return self::compose(
            new MapDecoder($f, $da->getName()),
            $da
        );
    }

    /**
     * @template T of bool | string | int
     *
     * @param T $l
     *
     * @return Decoder<mixed, T>
     */
    public static function literal(bool|string|int $l): Decoder
    {
        return new LiteralDecoder($l);
    }

    /**
     * @template I
     * @template T
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
     * @template MapOfDecoders of non-empty-array<array-key, Decoder<mixed, mixed>>
     *
     * @param MapOfDecoders $props
     *
     * @return Decoder<mixed, non-empty-array<array-key, mixed>>
     */
    public static function arrayProps(array $props): Decoder
    {
        return new ArrayPropsDecoder($props);
    }

    /**
     * @template T of object
     * @template Properties of non-empty-array<array-key, mixed>
     * @template ClassFactory of callable(...mixed):T
     *
     * @param Decoder<mixed, Properties> $propsDecoder
     * @param ClassFactory               $factory
     *
     * @return Decoder<mixed, T>
     */
    public static function classFromArrayPropsDecoder(
        Decoder $propsDecoder,
        callable $factory,
        string $decoderName
    ): Decoder {
        /** @psalm-var Decoder<Properties, T> $mapDecoder */
        $mapDecoder = new MapDecoder(
            fn(array $props) => Internal\FunctionUtils::destructureIn($factory)(\array_values($props)),
            \sprintf('%s(%s)', $decoderName, $propsDecoder->getName())
        );

        return self::pipe(
            $propsDecoder,
            $mapDecoder
        );
    }

    # ###########################################################
    #
    # Primitives
    #
    # ###########################################################

    /**
     * @template U
     *
     * @param U $default
     *
     * @return Decoder<mixed, U>
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
     * @return Decoder<mixed, bool>
     */
    public static function bool(): Decoder
    {
        return new BoolDecoder();
    }

    /**
     * @return Decoder<mixed, int>
     */
    public static function int(): Decoder
    {
        return new IntDecoder();
    }

    /**
     * @return Decoder<mixed, float>
     */
    public static function float(): Decoder
    {
        return new FloatDecoder();
    }

    /**
     * @return Decoder<mixed, string>
     */
    public static function string(): Decoder
    {
        return new StringDecoder();
    }

    /**
     * @return Decoder<mixed, mixed>
     */
    public static function mixed(): Decoder
    {
        return new MixedDecoder();
    }

    /**
     * @return Decoder<mixed, callable>
     */
    public static function callable(): Decoder
    {
        return new CallableDecoder();
    }

    # ###########################################################
    #
    # Useful decoders
    #
    # ###########################################################

    /**
     * @return Decoder<string, int>
     */
    public static function intFromString(): Decoder
    {
        return new IntFromStringDecoder();
    }

    /**
     * @return Decoder<string, \DateTimeInterface>
     */
    public static function dateTimeFromString(string $format = \DATE_ATOM): Decoder
    {
        return new DateTimeFromStringDecoder($format);
    }

    /**
     * @return Decoder<string, array<array-key, string>>
     */
    public static function regex(string $regex): Decoder
    {
        return new RegexDecoder($regex);
    }

    /**
     * @return Decoder<string, string>
     */
    public static function stringMatchingRegex(string $regex): Decoder
    {
        return new StringMatchingRegexDecoder($regex);
    }
}
