<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Arrays;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Internal\Arrays\ListRefiner;
use Facile\PhpCodec\Internal\Primitives\IntRefiner;
use PHPUnit\Framework\TestCase;

class ListRefineTest extends TestCase
{
    use TestTrait;

    public function testRefine(): void
    {
        $refine = new ListRefiner(new IntRefiner());

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
