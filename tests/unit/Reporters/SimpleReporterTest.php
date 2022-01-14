<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Reporters;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Reporters;
use function Facile\PhpCodec\strigify;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class SimpleReporterTest extends BaseTestCase
{
    use TestTrait;

    public function testReportBuildIn(): void
    {
        $d = Decoders::intFromString();
        $v = $d->decode('hello');
        self::assertEqualsCanonicalizing(
            ['Invalid value "hello" supplied to decoder "IntFromString"'],
            Reporters::simple()->report($v)
        );
    }

    public function testReportClass(): void
    {
        $type = Decoders::classFromArrayPropsDecoder(
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

        $reporter = Reporters::simple();

        self::assertEquals(
            ['/a: Invalid value 1 supplied to decoder "string"'],
            $reporter->report($type->decode(['a' => 1, 'b' => 2, 'c' => 1.23]))
        );

        self::assertEquals(
            [
                '/a: Invalid value 1 supplied to decoder "string"',
                '/b: Invalid value "ciao" supplied to decoder "int"',
                '/c: Invalid value undefined supplied to decoder "float"',
            ],
            $reporter->report($type->decode(['a' => 1, 'b' => 'ciao']))
        );

        self::assertEquals(
            [
                '/b: Invalid value "ciao" supplied to decoder "int"',
                '/c: Invalid value undefined supplied to decoder "float"',
            ],
            $reporter->report($type->decode(['a' => 'ciao', 'b' => 'ciao']))
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\oneOf(g\int(), g\float(), g\bool(), g\constant(null)),
                g\oneOf(g\string(), g\float(), g\bool(), g\constant(null)),
                g\oneOf(g\string(), g\int(), g\bool(), g\constant(null))
            )
            ->then(
                /**
                 * @psalm-param mixed $a
                 * @psalm-param mixed $b
                 * @psalm-param mixed $c
                 *
                 * @param mixed $a
                 * @param mixed $b
                 * @param mixed $c
                 */
                function ($a, $b, $c) use ($type, $reporter): void {
                    $errors = $reporter->report(
                        $type->decode([
                            'a' => $a,
                            'b' => $b,
                            'c' => $c,
                        ])
                    );

                    self::assertEquals(
                        [
                            \sprintf('/a: Invalid value %s supplied to decoder "string"', strigify($a)),
                            \sprintf('/b: Invalid value %s supplied to decoder "int"', strigify($b)),
                            \sprintf('/c: Invalid value %s supplied to decoder "float"', strigify($c)),
                        ],
                        $errors
                    );
                }
            );

        self::assertEquals(
            [
                'Invalid value "abc" supplied to decoder "Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})"',
            ],
            $reporter->report($type->decode('abc'))
        );
    }
}
