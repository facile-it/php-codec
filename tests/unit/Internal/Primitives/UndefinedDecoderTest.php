<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Internal\Undefined;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress UndefinedFunction
 */
class UndefinedDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testDefault(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $this
            ->forAll(GeneratorUtils::scalar())
            ->then(
                /** @psalm-param scalar $default */
                function ($default): void {
                    $x = self::assertValidationSuccess(
                        Codecs::undefined($default)->decode(new Undefined())
                    );
                    self::assertSame($default, $x);
                }
            );
    }
}
