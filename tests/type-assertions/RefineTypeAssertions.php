<?php declare(strict_types=1);

namespace TypeAssertions\Pybatt\Codec;

use Pybatt\Codec\Internal\Arrays\MapRefine;
use Pybatt\Codec\Internal\Primitives\InstanceOfRefine;
use Pybatt\Codec\Internal\Primitives\LitteralRefine;

class RefineTypeAssertions extends TypeAssertion
{
    public function assertInstanceOf(): void
    {
        $refiner = new InstanceOfRefine(\DateTimeInterface::class);

        $assert = function (\DateTimeInterface $_): void {
        };

        /** @var mixed $x */
        $x = self::mixed();
        if ($refiner->is($x)) {
            $assert($x);
        }
    }

    public function testRefineAssociativeArray(): void
    {
        $refiner = new MapRefine();

        /**
         * @param array<array-key,mixed> $x
         */
        function assert(array $x): void {}

        /** @var mixed $x */
        $x = self::mixed();
        if($refiner->is($x)) {
            assert($x);
        }
    }

    public function testRefineLitterals(): void
    {
        /** @var mixed $x */
        $x = self::mixed();

        if((new LitteralRefine('a'))->is($x)) {
            self::assertString($x);
        }

        if((new LitteralRefine(true))->is($x)) {
            self::assertBool($x);
            self::assertTrue($x);
        }

        if((new LitteralRefine(false))->is($x)) {
            self::assertFalse($x);
            self::assertTrue($x); // Why?
            self::assertBool($x);
        }

        if((new LitteralRefine(123))->is($x)) {
            self::assertInt($x);
        }
    }
}
