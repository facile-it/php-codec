<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;

/** @psalm-suppress PropertyNotSetInConstructor */
class DecodersTest extends BaseTestCase
{
    use TestTrait;

    public function testMap(): void
    {
        $decoder = Decoders::transformValidationSuccess(
            fn (int $v): DecodersTest\A => new DecodersTest\A($v),
            Decoders::int()
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::int()
            )
            ->then(function (int $i) use ($decoder): void {
                $a = self::assertSuccessInstanceOf(
                    DecodersTest\A::class,
                    $decoder->decode($i)
                );
                self::assertSame($i, $a->getValue());
            });
    }

    public function testStringDecoder(): void
    {
        /** @psalm-var string $input */
        $input = 'hello';

        self::asserSuccessSameTo(
            $input,
            Decoders::string()->decode($input)
        );
    }

    public function testNullDecoder(): void
    {
        self::asserSuccessSameTo(
            null,
            Decoders::null()->decode(null)
        );
    }

    public function testMixedDecoder(): void
    {
        $d = Decoders::mixed();

        $this
            ->forAll(
                Generators::oneOf(
                    Generators::int(),
                    Generators::string(),
                    Generators::bool(),
                    Generators::constant(null),
                    Generators::tuple([
                        'a' => Generators::int(),
                        'b' => Generators::string(),
                    ]),
                    Generators::date()
                )
            )
            ->then(
                /**
                 * @psalm-param mixed $any
                 *
                 * @psalm-return void
                 *
                 * @param mixed $any
                 */
                function ($any) use ($d): void {
                    self::asserSuccessSameTo($any, $d->decode($any));
                }
            );
    }
}

namespace Tests\Facile\PhpCodec\DecodersTest;

class A
{
    private int $v;

    public function __construct(int $v)
    {
        $this->v = $v;
    }

    public function getValue(): int
    {
        return $this->v;
    }
}
