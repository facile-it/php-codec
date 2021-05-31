<?php

declare(strict_types=1);

namespace TypeAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Internal\Arrays\MapRefiner;
use Facile\PhpCodec\Internal\Primitives\InstanceOfRefiner;

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
         * @psalm-suppress UnusedParam
         *
         * @var callable(array<array-key,mixed>):void
         */
        $assert = static function (array $x): void {
        };

        /** @var mixed $x */
        $x = self::mixed();
        if ($refiner->is($x)) {
            $assert($x);
        }
    }
}
