<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec;

use PHPUnit\Framework\TestCase;
use Pybatt\Codec\PathReporter;
use Pybatt\Codec\Validation;
use Pybatt\Codec\ValidationSuccess;

class BaseTestCase extends TestCase
{
    /**
     * @template T
     * @template R
     *
     * @param class-string $fqcn
     * @param Validation<T> $v
     * @param null | callable(T):R $thenDo
     * @return R | T
     */
    public static function asserSuccessInstanceOf(
        string $fqcn,
        Validation $v,
        callable $thenDo = null
    )
    {
        self::assertInstanceOf(
            ValidationSuccess::class,
            $v,
            implode("\n", (new PathReporter())->report($v))
        );
        /** @var ValidationSuccess<T> $v */
        $x = $v->getValue();
        self::assertInstanceOf($fqcn, $x);

        if (is_callable($thenDo)) {
            return $thenDo($x);
        }

        return $x;
    }
}
