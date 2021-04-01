<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Arrays;

use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Internal\Experimental\AssociativeArrayType;
use Facile\PhpCodec\PathReporter;
use PHPUnit\Framework\TestCase;

class AssociativeArrayTypeTest extends TestCase
{
    public function testMapType(): void
    {
        $map = new AssociativeArrayType([
            'a' => Codecs::null(),
            'b' => new AssociativeArrayType([
                'b1' => Codecs::int(),
                'b2' => Codecs::string(),
            ]),
            'c' => Codecs::string(),
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
