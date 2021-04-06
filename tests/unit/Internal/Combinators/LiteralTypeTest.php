<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codec;
use function Facile\PhpCodec\destructureIn;
use Facile\PhpCodec\Internal\Combinators\LiteralType;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

class LiteralTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        $this
            ->forAll(
                g\bind(
                    g\oneOf(
                        g\int(),
                        g\string(),
                        g\bool()
                    ),
                    function ($literal): g {
                        return g\tuple(
                            g\constant(new LiteralType($literal)),
                            g\oneOf(
                                GeneratorUtils::scalar(),
                                g\constant($literal)
                            ),
                            g\constant($literal)
                        );
                    }
                )
            )
            ->then(destructureIn(function (Codec $codec, $u, $a): void {
                self::codecLaws($codec)($u, $a);
            }));
    }
}
