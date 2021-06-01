<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

class IntersectionDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testStringIntersection(): void
    {
        $d = Decoders::intersection(
            Decoders::stringMatchingRegex('/^\d{2,4}$/'),
            Decoders::stringMatchingRegex('/^\d{3}$/')
        );

        self::asserSuccessSameTo(
            '123',
            $d->decode('123')
        );

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode('12')
        );

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode('1234')
        );
    }

    public function testIntersectionOfProps(): void
    {
        $d = Decoders::intersection(
            Decoders::arrayProps(['a' => Decoders::string()]),
            Decoders::arrayProps(['b' => Decoders::int()])
        );

        $this
            ->forAll(
                g\associative([
                    'a' => g\string(),
                    'b' => g\int(),
                ])
            )
            ->then(function ($i) use ($d): void {
                self::asserSuccessAnd(
                    $d->decode($i),
                    function (array $a): void {
                        self::assertIsString($a['a']);
                        self::assertIsInt($a['b']);
                    }
                );
            });

        $this
            ->forAll(
                g\oneOf(
                    g\associative([
                        'a' => g\oneOf(g\int(), g\float(), g\bool()),
                        'b' => g\int(),
                    ]),
                    g\associative([
                        'a' => g\string(),
                        'b' => g\oneOf(g\string(), g\float(), g\bool()),
                    ])
                )
            )
            ->then(function ($i) use ($d): void {
                self::assertInstanceOf(
                    ValidationFailures::class,
                    $d->decode($i)
                );
            });
    }

    public function testIntersectionOfUnionOfProps(): void
    {
        $d = Decoders::intersection(
            Decoders::arrayProps(['a' => Decoders::string()]),
            Decoders::union(
                Decoders::arrayProps([
                    'b' => Decoders::int(),
                    'c' => Decoders::bool(),
                ]),
                Decoders::arrayProps([
                    'b' => Decoders::null(),
                    'c' => Decoders::null(),
                ])
            )
        );

        $this
            ->forAll(
                g\oneOf(
                    g\associative([
                        'a' => g\string(),
                        'b' => g\int(),
                        'c' => g\bool(),
                    ]),
                    g\associative([
                        'a' => g\string(),
                        'b' => g\constant(null),
                        'c' => g\constant(null),
                    ])
                )
            )
            ->then(function ($i) use ($d): void {
                self::asserSuccessAnd(
                    $d->decode($i),
                    function (array $a): void {
                        self::assertIsString($a['a']);

                        if ($a['b'] === null) {
                            self::assertNull($a['c']);
                        } else {
                            self::assertIsInt($a['b']);
                            self::assertIsBool($a['c']);
                        }
                    }
                );
            });

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode(['a' => 'hei', 'b' => null, 'c' => false])
        );

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode(['a' => 'hei', 'b' => 1, 'c' => null])
        );
    }
}
