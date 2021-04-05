<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Internal\Primitives\BoolType;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

class BoolTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        $this
            ->forAll(
                GeneratorUtils::scalar(),
                g\bool()
            )
            ->then(self::codecLaws(Codecs::bool()));
    }
}
