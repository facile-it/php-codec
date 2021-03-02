<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\CommonTypes;

use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\CommonTypes\MapType;
use Pybatt\Codec\PathReporter;

class MapTypeTest extends TestCase
{
    public function testMapType(): void
    {
        $map = new MapType([
            'a' => Codecs::null(),
            'b' => new MapType([
                'b1' => Codecs::int(),
                'b2' => Codecs::string()
            ]),
            'c' => Codecs::string()
        ]);

        $r = $map->decode([
            'a' => 'ciao',
            'b' => [1, 2],
            'c' => 'ciao',
        ]);

        $reporter = new PathReporter();

        self::assertEquals(
            ['Invalid value {"a":"ciao","b":[1,2],"c":"ciao"} supplied to : {a: null, b: {b1: int, b2: string}, c: string}/a: null/b: {b1: int, b2: string}'],
            $reporter->report($r)
        );
    }
}
