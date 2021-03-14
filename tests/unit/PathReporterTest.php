<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec;

use Eris\Generator as g;
use Eris\TestTrait;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\PathReporter;
use Tests\Pybatt\Codec\PathReporterTest as in;
use function Pybatt\Codec\strigify;

class PathReporterTest extends BaseTestCase
{
    use TestTrait;

    public function testReportClass(): void
    {
        $type = Codecs::classFromArray(
            [
                'a' => Codecs::string(),
                'b' => Codecs::int(),
                'c' => Codecs::float()
            ],
            function (string $a, int $b, float $c): in\A {
                return new in\A($a, $b, $c);
            },
            in\A::class
        );

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1 supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/a: string'],
            $reporter->report($type->decode(['a' => 1, 'b' => 2, 'c' => 1.23]))
        );

        self::assertEquals(
            [
                'Invalid value 1 supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/a: string',
                'Invalid value "ciao" supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/b: int',
                'Invalid value undefined supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/c: float',
            ],
            $reporter->report($type->decode(['a' => 1, 'b' => 'ciao']))
        );

        self::assertEquals(
            [
                'Invalid value "ciao" supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/b: int',
                'Invalid value undefined supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/c: float',
            ],
            $reporter->report($type->decode(['a' => "ciao", 'b' => 'ciao']))
        );

        $this
            ->forAll(
                g\oneOf(g\int(), g\float(), g\bool(), g\constant(null)),
                g\oneOf(g\string(), g\float(), g\bool(), g\constant(null)),
                g\oneOf(g\string(), g\int(), g\bool(), g\constant(null))
            )
            ->then(function ($a, $b, $c) use ($type, $reporter) {
                $errors = $reporter->report(
                    $type->decode([
                        'a' => $a,
                        'b' => $b,
                        'c' => $c
                    ])
                );

                self::assertEquals(
                    [
                        sprintf('Invalid value %s supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/a: string', strigify($a)),
                        sprintf('Invalid value %s supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/b: int', strigify($b)),
                        sprintf('Invalid value %s supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})/c: float', strigify($c)),
                    ],
                    $errors
                );
            });

        self::assertEquals(
            [
                'Invalid value "abc" supplied to : Tests\Pybatt\Codec\PathReporterTest\A({a: string, b: int, c: float})'
            ],
            $reporter->report($type->decode('abc'))
        );
    }
}

namespace Tests\Pybatt\Codec\PathReporterTest;

class A
{
    public function __construct(
        string $a,
        int $b,
        float $d
    )
    {
    }
}
