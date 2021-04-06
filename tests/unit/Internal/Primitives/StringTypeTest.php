<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

class StringTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        $this
            ->forAll(
                GeneratorUtils::scalar(),
                g\string()
            )
            ->then(self::codecLaws(Codecs::string()));
    }
}
