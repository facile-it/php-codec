<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Facile\PhpCodec\Reporters\PathReporter;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * @psalm-template A
     * @psalm-template B of object
     *
     * @psalm-param class-string<B> $fqcn
     * @psalm-param Validation<A>   $v
     *
     * @psalm-return B
     *
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
     *
     * @psalm-param Validation<T> $v
     *
     * @psalm-assert ValidationSuccess<T> $v
     *
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
}
