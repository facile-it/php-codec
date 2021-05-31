<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Combinators\ComposeDecoder;
use Facile\PhpCodec\Internal\Combinators\LiteralDecoder;
use Facile\PhpCodec\Internal\Combinators\MapDecoder;
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
}
