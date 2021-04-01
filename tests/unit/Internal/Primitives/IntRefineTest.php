<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator;
use Eris\TestTrait;
use Facile\PhpCodec\Internal\Primitives\IntRefiner;
use PHPUnit\Framework\TestCase;

class IntRefineTest extends TestCase
{
    use TestTrait;

    public function testRefiner(): void
    {
        $refiner = new IntRefiner();

        $this
            ->forAll(
                Generator\int()
            )
            ->then(function ($i) use ($refiner) {
                self::assertTrue($refiner->is($i));
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\float(),
                    Generator\string(),
                    Generator\date(),
                    Generator\bool()
                )
            )
            ->then(function ($i) use ($refiner) {
                self::assertFalse($refiner->is($i));
            });
    }
}
