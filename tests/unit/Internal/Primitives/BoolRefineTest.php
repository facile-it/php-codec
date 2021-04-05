<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Internal\Primitives\BoolRefiner;
use PHPUnit\Framework\TestCase;

class BoolRefineTest extends TestCase
{
    use TestTrait;

    public function test(): void
    {
        $refine = new BoolRefiner();

        self::assertTrue($refine->is(true));
        self::assertTrue($refine->is(false));

        $this
            ->forAll(
                g\oneOf(g\string(), g\int(), g\float(), g\date())
            )
            ->then(function ($x) use ($refine): void {
                self::assertFalse($refine->is($x));
            });
    }
}
