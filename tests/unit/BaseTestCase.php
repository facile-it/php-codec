<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\PathReporter;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class BaseTestCase extends TestCase
{
    /**
     * @psalm-template T
     * @psalm-template R
     *
     * @param class-string         $fqcn
     * @param Validation<T>        $v
     * @param null | callable(T):R $thenDo
     *
     * @return R | T
     *
     * @deprecated
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
     * @psalm-template A of object
     * @psalm-template B of object
     *
     * @psalm-param class-string<B> $fqcn
     * @psalm-param Validation<A>   $v
     *
     * @psalm-return B
     * @psalm-assert ValidationSuccess<B> $v
     */
    public static function assertSuccessInstanceOf(
        string $fqcn,
        Validation $v
    ) {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );
        $x = $v->getValue();
        self::assertInstanceOf($fqcn, $x);

        return $x;
    }

    /**
     * @psalm-template T
     * @psalm-template R
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
     * @psalm-template T
     * @psalm-template R
     *
     * @param Validation<T> $v
     * @param callable(T):R $thenDo
     *
     * @return R
     *
     * @deprecated
     */
    public static function asserSuccessAnd(
        Validation $v,
        callable $thenDo
    ) {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );

        /** @var ValidationSuccess<T> $v */
        return $thenDo($v->getValue());
    }

    /**
     * @psalm-template T
     * @psalm-param Validation<T> $v
     * @psalm-assert ValidationSuccess<T> $v
     * @psalm-return T
     */
    public static function assertValidationSuccess(Validation $v)
    {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );

        /** @var T $value */
        $value = $v->getValue();

        return $value;
    }

    /**
     * @psalm-template I
     * @psalm-template A
     * @psalm-template O
     *
     * @param Codec<A, I, O> $codec
     *
     * @return \Closure(I, A): void
     */
    public static function codecLaws(
        Codec $codec
    ): \Closure {
        return function ($input, $a) use ($codec): void {
            self::assertCodecLaw1($codec, $input);
            self::assertCodecLaw2($codec, $a);
        };
    }

    /**
     * @psalm-template I
     * @psalm-template A
     * @psalm-template O
     *
     * @param Codec<A, I, O> $codec
     * @param I              $input
     */
    public static function assertCodecLaw1(
        Codec $codec,
        $input
    ): void {
        self::assertEquals(
            $input,
            Validation::fold(
                function () use ($input) {
                    return $input;
                },
                /** @psalm-param A $a */
                function ($a) use ($codec) {
                    return $codec->encode($a);
                },
                $codec->decode($input)
            )
        );
    }

    /**
     * @psalm-template A
     * @psalm-template I
     *
     * @param Codec<A, I, I> $codec
     * @param A              $a
     */
    public static function assertCodecLaw2(
        Codec $codec,
        $a
    ): void {
        $r = self::assertValidationSuccess($codec->decode($codec->encode($a)));

        self::assertEquals($a, $r);
    }
}
