<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

class CallableDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        $this
            ->forAll(
                g\oneOf(
                    GeneratorUtils::scalar(),
                    g\map(
                        function ($x): callable {
                            return static function () use ($x) {
                                return $x;
                            };
                        },
                        GeneratorUtils::scalar()
                    ),
                    g\map(
                        function ($x): callable {
                            return new class($x) {
                                private $n;

                                public function __construct($n)
                                {
                                    $this->n = $n;
                                }

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
                        function ($x): callable {
                            return static function () use ($x) {
                                return $x;
                            };
                        },
                        GeneratorUtils::scalar()
                    ),
                    g\map(
                        function ($x): callable {
                            return new class($x) {
                                private $n;

                                public function __construct($n)
                                {
                                    $this->n = $n;
                                }

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
