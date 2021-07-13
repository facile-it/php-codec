<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress UndefinedFunction
 */
class CallableDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $this
            ->forAll(
                g\oneOf(
                    GeneratorUtils::scalar(),
                    g\map(
                        /** @psalm-param scalar $x */
                        function ($x): callable {
                            return static function () use ($x) {
                                return $x;
                            };
                        },
                        GeneratorUtils::scalar()
                    ),
                    g\map(
                        /** @psalm-param scalar $x */
                        function ($x): callable {
                            return new class($x) {
                                /** @var scalar */
                                private $n;

                                /** @psalm-param scalar $n */
                                public function __construct($n)
                                {
                                    $this->n = $n;
                                }

                                /** @psalm-return scalar */
                                public function __invoke()
                                {
                                    return $this->n;
                                }
                            };
                        },
                        GeneratorUtils::scalar()
                    ),
                    g\constant('date'),
                    g\constant([T::class, 'm'])
                ),
                g\oneOf(
                    g\map(
                        /** @psalm-param scalar $x */
                        function ($x): callable {
                            return static function () use ($x) {
                                return $x;
                            };
                        },
                        GeneratorUtils::scalar()
                    ),
                    g\map(
                        /** @psalm-param scalar $x */
                        function ($x): callable {
                            return new class($x) {
                                /** @var scalar */
                                private $n;

                                /** @psalm-param scalar $n */
                                public function __construct($n)
                                {
                                    $this->n = $n;
                                }

                                /** @psalm-return scalar */
                                public function __invoke()
                                {
                                    return $this->n;
                                }
                            };
                        },
                        GeneratorUtils::scalar()
                    ),
                    g\constant('date'),
                    g\constant([T::class, 'm'])
                )
            )
            ->then(self::codecLaws(Codecs::callable()));
    }
}

class T
{
    public static function m(): int
    {
        return 1;
    }
}
