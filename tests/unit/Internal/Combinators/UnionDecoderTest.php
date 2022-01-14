<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Reporters\PathReporter;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class UnionDecoderTest extends TestCase
{
    public function testValidate(): void
    {
        $unionOfTwo = Decoders::union(Decoders::null(), Decoders::string());

        $r = $unionOfTwo->decode(1);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1 supplied to : null | string'],
            $reporter->report($r)
        );
    }

    public function testUnionOfThree(): void
    {
        $unionOfTwo = Decoders::union(Decoders::null(), Decoders::union(Decoders::string(), Decoders::int()));

        $r = $unionOfTwo->decode(1.2);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1.2 supplied to : null | string | int'],
            $reporter->report($r)
        );
    }
}
