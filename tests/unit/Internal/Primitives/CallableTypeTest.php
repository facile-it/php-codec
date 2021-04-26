<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

class CallableTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        $this
            ->forAll(
                GeneratorUtils::scalar(),
                static function (): void {
                },
                static function (int $a) {
                    return $a;
                }
            )
            ->then(self::codecLaws(Codecs::callable()));
    }
}
