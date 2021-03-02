<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\CommonTypes;

use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\CommonTypes\UnionType;
use Pybatt\Codec\PathReporter;

class UnionTypeTest extends TestCase
{
    public function testValidate()
    {
        $unionOfTwo = new UnionType(Codecs::null(), Codecs::string());

        $r = $unionOfTwo->decode(1);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1 supplied to : null | string/: null | string'],
            $reporter->report($r)
        );
    }

    public function testUnionOfThree(): void
    {

        $unionOfTwo = new UnionType(Codecs::null(), new UnionType(Codecs::string(), Codecs::int()));

        $r = $unionOfTwo->decode(1.2);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value 1.2 supplied to : null | string | int/: null | string | int'],
            $reporter->report($r)
        );

    }
}
