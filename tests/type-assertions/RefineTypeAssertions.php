<?php declare(strict_types=1);

namespace TypeAssertions\Pybatt\Codec;

use Pybatt\Codec\Internal\Arrays\MapRefiner;
use Pybatt\Codec\Internal\Primitives\InstanceOfRefiner;
use Pybatt\Codec\Internal\Primitives\LitteralRefiner;

class RefineTypeAssertions extends TypeAssertion
{
    public function assertInstanceOf(): void
    {
        $refiner = new InstanceOfRefiner(\DateTimeInterface::class);

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
        $refiner = new MapRefiner();

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

        if((new LitteralRefiner('a'))->is($x)) {
            self::assertString($x);
        }

        if((new LitteralRefiner(true))->is($x)) {
            self::assertBool($x);
            self::assertTrue($x);
        }

        if((new LitteralRefiner(false))->is($x)) {
            self::assertFalse($x);
            self::assertTrue($x); // Why?
            self::assertBool($x);
        }

        if((new LitteralRefiner(123))->is($x)) {
            self::assertInt($x);
        }
    }
}
