<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\PathReporter;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * @template T
     * @template R
     *
     * @param class-string         $fqcn
     * @param Validation<T>        $v
     * @param null | callable(T):R $thenDo
     *
     * @return R | T
     */
    public static function asserSuccessInstanceOf(
        string $fqcn,
        Validation $v,
        ?callable $thenDo = null
    ) {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );
        /** @var ValidationSuccess<T> $v */
        $x = $v->getValue();
        self::assertInstanceOf($fqcn, $x);

        if (\is_callable($thenDo)) {
            return $thenDo($x);
        }

        return $x;
    }

    /**
     * @template T
     * @template R
     *
     * @param T             $expected
     * @param Validation<T> $v
     *
     * @return T
     */
    public static function asserSuccessSameTo(
        $expected,
        Validation $v
    ) {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );

        /** @var ValidationSuccess<T> $v */
        $x = $v->getValue();
        self::assertSame($expected, $x);

        return $x;
    }

    /**
     * @template T
     * @template R
     *
     * @param Validation<T> $v
     * @param callable(T):R $thenDo
     *
     * @return R
     */
    public static function asserSuccessAnd(
        Validation $v,
        callable $thenDo
    )
    {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );

        /** @var ValidationSuccess<T> $v */
        return $thenDo($v->getValue());
    }

    /**
     * @template I
     * @template A
     * @template O
     *
     * @param Codec<A, I, O> $codec
     *
     * @return \Closure(I, A): void
     */
    public static function codecLaws(
        Codec $codec
    ): \Closure
    {
        return function ($input, $a) use ($codec): void {
            self::assertCodecLaw1($codec, $input);
            self::assertCodecLaw2($codec, $a);
        };
    }

    /**
     * @template I
     * @template A
     * @template O
     *
     * @param Codec<A, I, O> $codec
     * @param I $input
     */
    public static function assertCodecLaw1(
        Codec $codec,
        $input
    ): void
    {
        self::assertEquals(
            $input,
            Validation::fold(
                function () use ($input) {
                    return $input;
                },
                function ($a) use ($codec) {
                    return $codec->encode($a);
                },
                $codec->decode($input)
            )
        );
    }

    /**
     * @template A
     * @template I
     * @template O
     *
     * @param Codec<A, I, O> $codec
     * @param A $a
     */
    public static function assertCodecLaw2(
        Codec $codec,
        $a
    ): void
    {
        self::asserSuccessAnd(
            $codec->decode($codec->encode($a)),
            function ($r) use ($a): void {
                self::assertEquals($a, $r);
            }
        );
    }
}
