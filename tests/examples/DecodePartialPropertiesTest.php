<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec;

use Facile\PhpCodec\Decoders;
use Tests\Facile\PhpCodec\BaseTestCase;

class DecodePartialPropertiesTest extends BaseTestCase
{
    public function test(): void
    {
        $c = Decoders::classFromArrayPropsDecoder(
            Decoders::arrayProps([
                'foo' => Decoders::string(),
                'bar' => Decoders::union(Decoders::int(), Decoders::undefined(-1)),
            ]),
            fn(string $foo, int $bar): DecodePartialPropertiesTest\A => new DecodePartialPropertiesTest\A($foo, $bar),
            DecodePartialPropertiesTest\A::class
        );

        $a = self::assertSuccessInstanceOf(
            DecodePartialPropertiesTest\A::class,
            $c->decode(['foo' => 'str'])
        );

        self::assertSame('str', $a->getFoo());
        self::assertSame(-1, $a->getBar());
    }
}

namespace Examples\Facile\PhpCodec\DecodePartialPropertiesTest;

class A
{
    public function __construct(private readonly string $foo, private readonly int $bar) {}

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): int
    {
        return $this->bar;
    }
}
