<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Facile\PhpCodec\Decoders;

class DecodersTest extends BaseTestCase
{
    public function testMap(): void
    {
        $decoder = Decoders::map(
            function (int $v): DecodersTest\A {
                return new DecodersTest\A($v);
            },
            Decoders::int()
        );

        self::asserSuccessInstanceOf(
            DecodersTest\A::class,
            $decoder->decode(1)
        );
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
}
