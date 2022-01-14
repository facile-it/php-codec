<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Reporters\PathReporter;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class ArrayPropsDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testProps(): void
    {
        $d = Decoders::arrayProps([
            'a' => Decoders::string(),
            'b' => Decoders::int(),
            'c' => Decoders::bool(),
            'd' => Decoders::literal('hello'),
        ]);

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\associative([
                    'a' => g\string(),
                    'b' => g\int(),
                    'c' => g\bool(),
                    'd' => g\constant('hello'),
                ])
            )
            ->then(function (array $i) use ($d): void {
                $a = self::assertValidationSuccess(
                    $d->decode($i)
                );

                self::assertIsString($a['a']);
                self::assertIsInt($a['b']);
                self::assertIsBool($a['c']);
                self::assertSame('hello', $a['d']);
            });

        $msgs = PathReporter::create()
            ->report($d->decode(['a' => 'str', 'b' => 'hei', 'c' => true, 'd' => 'world']));

        self::assertEquals(
            [
                'Invalid value "hei" supplied to : {a: string, b: int, c: bool, d: \'hello\'}/b: int',
                'Invalid value "world" supplied to : {a: string, b: int, c: bool, d: \'hello\'}/d: \'hello\'',
            ],
            $msgs
        );
    }
}
