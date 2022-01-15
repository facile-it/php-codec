<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Reporters;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Reporter;
use Facile\PhpCodec\Reporters;
use function Facile\PhpCodec\strigify;
use Facile\PhpCodec\Validation\Validation;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class ReportersTest extends BaseTestCase
{
    use TestTrait;

    /**
     * @param Reporter $reporter
     * @param array    $expected
     *
     * @return void
     * @dataProvider provideReportRootErrors
     */
    public function testReportRootError(
        Reporter $reporter,
        array $expected
    ): void {
        self::assertReports(
            $expected,
            $reporter,
            Decoders::intFromString()->decode('hello')
        );
    }

    public function provideReportRootErrors(): array
    {
        return [
            [
                Reporters::path(),
                ['Invalid value "hello" supplied to : IntFromString'],
            ],
            [
                Reporters::simplePath(),
                ['Invalid value "hello" supplied to decoder "IntFromString"'],
            ],
        ];
    }

    /**
     * @param Reporter $reporter
     * @param mixed    $value
     * @param array    $expected
     *
     * @return void
     * @dataProvider provideReportRootClassError
     */
    public function testReportRootClassError(
        Reporter $reporter,
        $value,
        array $expected
    ): void {
        $decoder = Decoders::classFromArrayPropsDecoder(
            Decoders::arrayProps([
                'a' => Decoders::string(),
                'b' => Decoders::int(),
                'c' => Decoders::float(),
            ]),
            static function (string $a, int $b, float $c): Models\A {
                return new Models\A($a, $b, $c);
            },
            Models\A::class
        );

        self::assertReports(
            $expected,
            $reporter,
            $decoder->decode($value)
        );
    }

    public function provideReportRootClassError(): array
    {
        return [
            [
                Reporters::path(),
                ['a' => 1, 'b' => 2, 'c' => 1.23],
                ['Invalid value 1 supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/a: string'],
            ],
            [
                Reporters::path(),
                ['a' => 1, 'b' => 'ciao'],
                [
                    'Invalid value 1 supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/a: string',
                    'Invalid value "ciao" supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/b: int',
                    'Invalid value undefined supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/c: float',
                ],
            ],
            [
                Reporters::path(),
                ['a' => 'ciao', 'b' => 'ciao'],
                [
                    'Invalid value "ciao" supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/b: int',
                    'Invalid value undefined supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/c: float',
                ],
            ],
            [
                Reporters::path(),
                'abc',
                ['Invalid value "abc" supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})'],
            ],
            [
                Reporters::simplePath(),
                ['a' => 1, 'b' => 2, 'c' => 1.23],
                ['/a: Invalid value 1 supplied to decoder "string"'],
            ],
            [
                Reporters::simplePath(),
                ['a' => 1, 'b' => 'ciao'],
                [
                    '/a: Invalid value 1 supplied to decoder "string"',
                    '/b: Invalid value "ciao" supplied to decoder "int"',
                    '/c: Invalid value undefined supplied to decoder "float"',
                ],
            ],
            [
                Reporters::simplePath(),
                ['a' => 'ciao', 'b' => 'ciao'],
                [
                    '/b: Invalid value "ciao" supplied to decoder "int"',
                    '/c: Invalid value undefined supplied to decoder "float"',
                ],
            ],
            [
                Reporters::simplePath(),
                'abc',
                ['Invalid value "abc" supplied to decoder "Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})"'],
            ],
        ];
    }

    public function testReportClass(): void
    {
        $decoder = Decoders::classFromArrayPropsDecoder(
            Decoders::arrayProps([
                'a' => Decoders::string(),
                'b' => Decoders::int(),
                'c' => Decoders::float(),
            ]),
            static function (string $a, int $b, float $c): Models\A {
                return new Models\A($a, $b, $c);
            },
            Models\A::class
        );

        $pathReporter = Reporters::path();
        $simplePathReporter = Reporters::simplePath();

        /** @psalm-suppress TooManyArguments */
        $this
            ->forAll(
                Generators::associative([
                    'a' => Generators::oneOf(Generators::int(), Generators::float(), Generators::bool(), Generators::constant(null)),
                    'b' => Generators::oneOf(Generators::string(), Generators::float(), Generators::bool(), Generators::constant(null)),
                    'c' => Generators::oneOf(Generators::string(), Generators::int(), Generators::bool(), Generators::constant(null)),
                ])
            )
            ->then(
                function (array $value) use ($simplePathReporter, $pathReporter, $decoder): void {
                    $validation = $decoder->decode($value);

                    self::assertReports(
                        [
                            \sprintf('Invalid value %s supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/a: string', strigify($value['a'])),
                            \sprintf('Invalid value %s supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/b: int', strigify($value['b'])),
                            \sprintf('Invalid value %s supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/c: float', strigify($value['c'])),
                        ],
                        $pathReporter,
                        $validation
                    );

                    self::assertReports(
                        [
                            \sprintf('/a: Invalid value %s supplied to decoder "string"', strigify($value['a'])),
                            \sprintf('/b: Invalid value %s supplied to decoder "int"', strigify($value['b'])),
                            \sprintf('/c: Invalid value %s supplied to decoder "float"', strigify($value['c'])),
                        ],
                        $simplePathReporter,
                        $validation
                    );
                }
            );
    }

    private static function assertReports(array $expected, Reporter $reporter, Validation $validation): void
    {
        self::assertEqualsCanonicalizing(
            $expected,
            $reporter->report($validation)
        );
    }
}
