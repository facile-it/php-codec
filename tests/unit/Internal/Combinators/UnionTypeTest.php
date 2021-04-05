<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Internal\Combinators\UnionCodec;
use Facile\PhpCodec\PathReporter;
use PHPUnit\Framework\TestCase;

class UnionTypeTest extends TestCase
{
    public function testValidate(): void
    {
        $unionOfTwo = new UnionCodec(Codecs::null(), Codecs::string());

        $r = $unionOfTwo->decode(1);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1 supplied to : null | string'],
            $reporter->report($r)
        );
    }

    public function testUnionOfThree(): void
    {
        $unionOfTwo = new UnionCodec(Codecs::null(), new UnionCodec(Codecs::string(), Codecs::int()));

        $r = $unionOfTwo->decode(1.2);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1.2 supplied to : null | string | int'],
            $reporter->report($r)
        );
    }
}
