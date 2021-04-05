<?php declare(strict_types=1);

namespace Examples\Facile\PhpCodec;

use Facile\PhpCodec\Codecs;
use Tests\Facile\PhpCodec\BaseTestCase;

class DecodePartialPropertiesTest extends BaseTestCase
{
    public function test(): void
    {
        $c = Codecs::classFromArray(
            [
                'foo' => Codecs::string(),
                'bar' => Codecs::union(Codecs::int(), Codecs::undefined(-1))
            ],
            function (string $foo, int $bar): DecodePartialPropertiesTest\A {
                return new DecodePartialPropertiesTest\A($foo, $bar);
            },
            DecodePartialPropertiesTest\A::class
        );

        self::asserSuccessInstanceOf(
            DecodePartialPropertiesTest\A::class,
            $c->decode(['foo' => 'str']),
            function (DecodePartialPropertiesTest\A $a) {
                self::assertSame('str', $a->getFoo());
                self::assertSame(-1, $a->getBar());
            }
        );
    }
}

namespace Examples\Facile\PhpCodec\DecodePartialPropertiesTest;

class A
{
    /** @var string */
    private $foo;
    /** @var int */
    private $bar;

    public function __construct(
        string $foo,
        int $bar
    )
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): int
    {
        return $this->bar;
    }
}
