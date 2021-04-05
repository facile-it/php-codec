<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

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
    ) {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            \implode("\n", PathReporter::create()->report($v))
        );

        /** @var ValidationSuccess<T> $v */
        return $thenDo($v->getValue());
    }
}
