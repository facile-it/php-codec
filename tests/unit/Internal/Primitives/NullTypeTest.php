<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress UndefinedFunction
 */
class NullTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $this
            ->forAll(
                Generators::oneOf(
                    GeneratorUtils::scalar(),
                    Generators::constant(null)
                ),
                Generators::constant(null)
            )
            ->then(self::codecLaws(Codecs::null()));
    }
}
