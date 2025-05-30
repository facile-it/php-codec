<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;

class DecodersTest extends BaseTestCase
{
    use TestTrait;

    public function testMap(): void
    {
        $decoder = Decoders::transformValidationSuccess(
            fn(int $v): DecodersTest\A => new DecodersTest\A($v),
            Decoders::int()
        );

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
}

namespace Tests\Facile\PhpCodec\DecodersTest;

class A
{
    public function __construct(private readonly int $v) {}

    public function getValue(): int
    {
        return $this->v;
    }
}
