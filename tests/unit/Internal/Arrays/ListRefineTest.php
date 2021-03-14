<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Internal\Arrays;

use Eris\Generator as g;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Internal\Arrays\ListRefine;
use Pybatt\Codec\Internal\Primitives\IntRefine;

class ListRefineTest extends TestCase
{
    use TestTrait;

    public function testRefine(): void
    {
        $refine = new ListRefine(new IntRefine());

        $this
            ->forAll(
                self::generateList(g\int())
            )
            ->then(function ($l) use ($refine) {
                self::assertTrue($refine->is($l));
            });
    }

    private static function generateList(g $elemGenerator): g
    {
        return g\bind(
            g\choose(0, 10),
            function (int $size) use ($elemGenerator): g {
                return g\vector($size, $elemGenerator);
            }
        );
    }
}
