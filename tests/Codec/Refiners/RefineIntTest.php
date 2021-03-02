<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Refiners;

use Eris\Generator;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Primitives\RefineInt;

class RefineIntTest extends TestCase
{
    use TestTrait;

    public function testRefiner(): void
    {
        $refiner = new RefineInt();

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
