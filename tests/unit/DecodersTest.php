<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generator;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;

class DecodersTest extends BaseTestCase
{
    use TestTrait;

    public function testMap(): void
    {
        $decoder = Decoders::map(
            function (int $v): DecodersTest\A {
                return new DecodersTest\A($v);
            },
            Decoders::int()
        );

        $this
            ->forAll(
                Generator\int()
            )
            ->then(function (int $i) use ($decoder): void {
                self::asserSuccessInstanceOf(
                    DecodersTest\A::class,
                    $decoder->decode($i),
                    function (DecodersTest\A $a) use ($i): void {
                        self::assertSame($i, $a->getValue());
                    }
                );
            });
    }
}

namespace Tests\Facile\PhpCodec\DecodersTest;

class A
{
    /** @var int */
    private $v;

    public function __construct(int $v)
    {
        $this->v = $v;
    }

    public function getValue(): int
    {
        return $this->v;
    }
}
