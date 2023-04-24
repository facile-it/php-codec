<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class IntersectionDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testStringIntersection(): void
    {
        $d = Decoders::intersection(
            Decoders::stringMatchingRegex('/^[123]{2,4}$/'),
            Decoders::stringMatchingRegex('/^\d{3}$/')
        );

        /** @psalm-var string $input
         */
        $input = '123';
        self::asserSuccessSameTo(
            $input,
            $d->decode($input)
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

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::associative([
                    'a' => Generators::string(),
                    'b' => Generators::int(),
                ])
            )
            ->then(function (array $i) use ($d): void {
                $a = self::assertValidationSuccess(
                    $d->decode($i)
                );
                self::assertIsString($a['a']);
                self::assertIsInt($a['b']);
            });

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::oneOf(
                    Generators::associative([
                        'a' => Generators::oneOf(Generators::int(), Generators::float(), Generators::bool()),
                        'b' => Generators::int(),
                    ]),
                    Generators::associative([
                        'a' => Generators::string(),
                        'b' => Generators::oneOf(Generators::string(), Generators::float(), Generators::bool()),
                    ])
                )
            )
            ->then(function (array $i) use ($d): void {
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

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::oneOf(
                    Generators::associative([
                        'a' => Generators::string(),
                        'b' => Generators::int(),
                        'c' => Generators::bool(),
                    ]),
                    Generators::associative([
                        'a' => Generators::string(),
                        'b' => Generators::constant(null),
                        'c' => Generators::constant(null),
                    ])
                )
            )
            ->then(function (array $i) use ($d): void {
                $a = self::assertValidationSuccess(
                    $d->decode($i)
                );
                self::assertIsString($a['a']);

                if ($a['b'] === null) {
                    self::assertNull($a['c']);
                } else {
                    self::assertIsInt($a['b']);
                    self::assertIsBool($a['c']);
                }
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
