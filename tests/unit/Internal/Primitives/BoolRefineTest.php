<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Internal\Primitives;

use Eris\TestTrait;
use Eris\Generator as g;
use Pybatt\Codec\Internal\Primitives\BoolRefine;
use PHPUnit\Framework\TestCase;

class BoolRefineTest extends TestCase
{
    use TestTrait;

    public function test(): void
    {
        $refine = new BoolRefine();

        self::assertTrue($refine->is(true));
        self::assertTrue($refine->is(false));

        $this
            ->forAll(
                g\oneOf(g\string(), g\int(), g\float(), g\date())
            )
            ->then(function ($x) use ($refine) {
                self::assertFalse($refine->is($x));
            });
    }
}
