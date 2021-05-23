<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Combinators\ComposeDecoder;
use Facile\PhpCodec\Internal\Combinators\MapDecoder;
use Facile\PhpCodec\Internal\Primitives\CallableDecoder;
use Facile\PhpCodec\Internal\Primitives\IntDecoder;
use Facile\PhpCodec\Internal\Primitives\MixedDecoder;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Internal\Useful\IntFromStringDecoder;
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
     * @psalm-return Decoder<mixed, int>
     */
    public static function int(): Decoder
    {
        return new IntDecoder();
    }

    /**
     * @psalm-return Decoder<string, int>
     */
    public static function intFromString(): Decoder
    {
        return new IntFromStringDecoder();
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
}
