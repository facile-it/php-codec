<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Reporters;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Reporter;
use Facile\PhpCodec\Reporters;
use Facile\PhpCodec\Validation\Validation;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\Reporters\Models\SampleClass;

/** @psalm-suppress PropertyNotSetInConstructor */
class ReportersTest extends BaseTestCase
{
    use TestTrait;

    /**
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
     * @param mixed $value
     *
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
            static fn(string $a, int $b, float $c): Models\A => new Models\A($a, $b, $c),
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
            static fn(string $a, int $b, float $c): Models\A => new Models\A($a, $b, $c),
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
                            \sprintf('Invalid value %s supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/a: string', FunctionUtils::strigify($value['a'])),
                            \sprintf('Invalid value %s supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/b: int', FunctionUtils::strigify($value['b'])),
                            \sprintf('Invalid value %s supplied to : Tests\Facile\PhpCodec\Reporters\Models\A({a: string, b: int, c: float})/c: float', FunctionUtils::strigify($value['c'])),
                        ],
                        $pathReporter,
                        $validation
                    );

                    self::assertReports(
                        [
                            \sprintf('/a: Invalid value %s supplied to decoder "string"', FunctionUtils::strigify($value['a'])),
                            \sprintf('/b: Invalid value %s supplied to decoder "int"', FunctionUtils::strigify($value['b'])),
                            \sprintf('/c: Invalid value %s supplied to decoder "float"', FunctionUtils::strigify($value['c'])),
                        ],
                        $simplePathReporter,
                        $validation
                    );
                }
            );
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideNestedArrayPropsReport
     */
    public function testNestedArrayPropsReport(Reporter $reporter, $value, array $expected): void
    {
        $decoder = Decoders::arrayProps([
            'a' => Decoders::arrayProps([
                'a1' => Decoders::int(),
                'a2' => Decoders::string(),
            ]),
            'b' => Decoders::arrayProps(['b1' => Decoders::bool()]),
        ]);

        self::assertReports(
            $expected,
            $reporter,
            $decoder->decode($value)
        );
    }

    public function provideNestedArrayPropsReport(): array
    {
        return [
            [
                Reporters::path(),
                ['a' => ['a1' => 'str', 'a2' => 1], 'b' => []],
                [
                    'Invalid value "str" supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/a: {a1: int, a2: string}/a1: int',
                    'Invalid value 1 supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/a: {a1: int, a2: string}/a2: string',
                    'Invalid value undefined supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/b: {b1: bool}/b1: bool',
                ],
            ],
            [
                Reporters::path(),
                ['a' => ['a1' => 'str', 'a2' => 1], 'b' => 2],
                [
                    'Invalid value "str" supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/a: {a1: int, a2: string}/a1: int',
                    'Invalid value 1 supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/a: {a1: int, a2: string}/a2: string',
                    'Invalid value 2 supplied to : {a: {a1: int, a2: string}, b: {b1: bool}}/b: {b1: bool}',
                ],
            ],
            [
                Reporters::simplePath(),
                ['a' => ['a1' => 'str', 'a2' => 1], 'b' => []],
                [
                    '/a/a1: Invalid value "str" supplied to decoder "int"',
                    '/a/a2: Invalid value 1 supplied to decoder "string"',
                    '/b/b1: Invalid value undefined supplied to decoder "bool"',
                ],
            ],
            [
                Reporters::simplePath(),
                ['a' => ['a1' => 'str', 'a2' => 1], 'b' => 2],
                [
                    '/a/a1: Invalid value "str" supplied to decoder "int"',
                    '/a/a2: Invalid value 1 supplied to decoder "string"',
                    '/b: Invalid value 2 supplied to decoder "{b1: bool}"',
                ],
            ],
            [
                Reporters::simplePath(),
                ['b' => 2],
                [
                    '/a: Invalid value undefined supplied to decoder "{a1: int, a2: string}"',
                    '/b: Invalid value 2 supplied to decoder "{b1: bool}"',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideListOfClassReport
     */
    public function testListOfClassReport(Reporter $reporter, $value, array $expected): void
    {
        $decoder = Decoders::listOf(
            Decoders::classFromArrayPropsDecoder(
                Decoders::arrayProps([
                    'name' => Decoders::string(),
                    'number' => Decoders::int(),
                    'amount' => Decoders::float(),
                    'flag' => Decoders::bool(),
                ]),
                static fn(string $name, int $number, float $amount, bool $flag) => new SampleClass($name, $number, $amount, $flag),
                'SampleClass'
            )
        );

        self::assertReports(
            $expected,
            $reporter,
            $decoder->decode($value)
        );
    }

    public function provideListOfClassReport(): array
    {
        return [
            [
                Reporters::path(),
                [null],
                ['Invalid value null supplied to : SampleClass({name: string, number: int, amount: float, flag: bool})[]/0: SampleClass({name: string, number: int, amount: float, flag: bool})'],
            ],
            [
                Reporters::path(),
                [
                    ['name' => 'tom', 'number' => 1, 'amount' => true, 'flag' => 1.3],
                    ['name' => 2, 'number' => 2],
                ],
                [
                    'Invalid value true supplied to : SampleClass({name: string, number: int, amount: float, flag: bool})[]/0: SampleClass({name: string, number: int, amount: float, flag: bool})/amount: float',
                    'Invalid value 1.3 supplied to : SampleClass({name: string, number: int, amount: float, flag: bool})[]/0: SampleClass({name: string, number: int, amount: float, flag: bool})/flag: bool',
                ],
            ],
            [
                Reporters::path(),
                [
                    ['name' => 'tom', 'number' => 1, 'amount' => 1.3, 'flag' => true],
                    ['name' => 2, 'number' => 2],
                ],
                [
                    'Invalid value undefined supplied to : SampleClass({name: string, number: int, amount: float, flag: bool})[]/1: SampleClass({name: string, number: int, amount: float, flag: bool})/amount: float',
                    'Invalid value 2 supplied to : SampleClass({name: string, number: int, amount: float, flag: bool})[]/1: SampleClass({name: string, number: int, amount: float, flag: bool})/name: string',
                    'Invalid value undefined supplied to : SampleClass({name: string, number: int, amount: float, flag: bool})[]/1: SampleClass({name: string, number: int, amount: float, flag: bool})/flag: bool',
                ],
            ],
            [
                Reporters::simplePath(),
                [null],
                ['/0: Invalid value null supplied to decoder "SampleClass({name: string, number: int, amount: float, flag: bool})"'],
            ],
            [
                Reporters::simplePath(),
                [
                    ['name' => 'tom', 'number' => 1, 'amount' => true, 'flag' => 1.3],
                    ['name' => 2, 'number' => 2],
                ],
                [
                    '/0/amount: Invalid value true supplied to decoder "float"',
                    '/0/flag: Invalid value 1.3 supplied to decoder "bool"',
                ],
            ],
            [
                Reporters::simplePath(),
                [
                    ['name' => 'tom', 'number' => 1, 'amount' => 1.3, 'flag' => true],
                    ['name' => 2, 'number' => 2],
                ],
                [
                    '/1/amount: Invalid value undefined supplied to decoder "float"',
                    '/1/flag: Invalid value undefined supplied to decoder "bool"',
                    '/1/name: Invalid value 2 supplied to decoder "string"',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideUnionReport
     */
    public function testUnionReport(Reporter $reporter, $value, array $expected): void
    {
        $decoder = Decoders::arrayProps([
            'a' => Decoders::union(
                Decoders::arrayProps([
                    'a1' => Decoders::int(),
                    'a2' => Decoders::float(),
                ]),
                Decoders::arrayProps([
                    'b1' => Decoders::string(),
                    'b2' => Decoders::bool(),
                ]),
                Decoders::arrayProps([
                    'c1' => Decoders::pipe(Decoders::string(), Decoders::regex('/^\d*$/')),
                ])
            ),
        ]);

        self::assertReports(
            $expected,
            $reporter,
            $decoder->decode($value)
        );
    }

    public function provideUnionReport(): array
    {
        return [
            [
                Reporters::path(),
                null,
                [
                    'Invalid value null supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}',
                ],
            ],
            [
                Reporters::path(),
                [],
                [
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/0: {a1: int, a2: float}',
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/1: {b1: string, b2: bool}',
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/2: {c1: regex(/^\d*$/)}',
                ],
            ],
            [
                Reporters::path(),
                ['a' => ['a1' => 1]],
                [
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/0: {a1: int, a2: float}/a2: float',
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/1: {b1: string, b2: bool}/b1: string',
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/1: {b1: string, b2: bool}/b2: bool',
                    'Invalid value undefined supplied to : {a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}/a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}/2: {c1: regex(/^\d*$/)}/c1: regex(/^\d*$/)',
                ],
            ],
            [
                Reporters::simplePath(),
                null,
                [
                    'Invalid value null supplied to decoder "{a: {a1: int, a2: float} | {b1: string, b2: bool} | {c1: regex(/^\d*$/)}}"',
                ],
            ],
            [
                Reporters::simplePath(),
                [],
                [
                    '/a/0: Invalid value undefined supplied to decoder "{a1: int, a2: float}"',
                    '/a/1: Invalid value undefined supplied to decoder "{b1: string, b2: bool}"',
                    '/a/2: Invalid value undefined supplied to decoder "{c1: regex(/^\d*$/)}"',
                ],
            ],
            [
                Reporters::simplePath(),
                ['a' => ['a1' => 1]],
                [
                    '/a/0/a2: Invalid value undefined supplied to decoder "float"',
                    '/a/1/b1: Invalid value undefined supplied to decoder "string"',
                    '/a/1/b2: Invalid value undefined supplied to decoder "bool"',
                    '/a/2/c1: Invalid value undefined supplied to decoder "regex(/^\d*$/)"',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideIntersectionReport
     */
    public function testIntersectionReport(
        Reporter $reporter,
        $value,
        array $expected
    ): void {
        $d = Decoders::intersection(
            Decoders::arrayProps(['a' => Decoders::string()]),
            Decoders::arrayProps(['b' => Decoders::int()])
        );

        self::assertReports(
            $expected,
            $reporter,
            $d->decode($value)
        );
    }

    public function provideIntersectionReport(): array
    {
        return [
            [
                Reporters::path(),
                null,
                [
                    'Invalid value null supplied to : {a: string} & {b: int}/0: {a: string}',
                    'Invalid value null supplied to : {a: string} & {b: int}/1: {b: int}',
                ],
            ],
            [
                Reporters::path(),
                [],
                [
                    'Invalid value undefined supplied to : {a: string} & {b: int}/0: {a: string}/a: string',
                    'Invalid value undefined supplied to : {a: string} & {b: int}/1: {b: int}/b: int',
                ],
            ],
            [
                Reporters::path(),
                ['a' => 'hello'],
                ['Invalid value undefined supplied to : {a: string} & {b: int}/1: {b: int}/b: int'],
            ],
            [
                Reporters::path(),
                ['b' => 'hello'],
                [
                    'Invalid value undefined supplied to : {a: string} & {b: int}/0: {a: string}/a: string',
                    'Invalid value "hello" supplied to : {a: string} & {b: int}/1: {b: int}/b: int',
                ],
            ],
            [
                Reporters::simplePath(),
                null,
                [
                    '/0: Invalid value null supplied to decoder "{a: string}"',
                    '/1: Invalid value null supplied to decoder "{b: int}"',
                ],
            ],
            [
                Reporters::simplePath(),
                [],
                [
                    '/0/a: Invalid value undefined supplied to decoder "string"',
                    '/1/b: Invalid value undefined supplied to decoder "int"',
                ],
            ],
            [
                Reporters::simplePath(),
                ['a' => 'hello'],
                [
                    '/1/b: Invalid value undefined supplied to decoder "int"',
                ],
            ],
            [
                Reporters::simplePath(),
                ['b' => 'hello'],
                [
                    '/0/a: Invalid value undefined supplied to decoder "string"',
                    '/1/b: Invalid value "hello" supplied to decoder "int"',
                ],
            ],
        ];
    }

    private static function assertReports(array $expected, Reporter $reporter, Validation $validation): void
    {
        self::assertEqualsCanonicalizing(
            $expected,
            $reporter->report($validation)
        );
    }
}
