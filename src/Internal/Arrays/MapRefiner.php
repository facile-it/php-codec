<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Arrays;

use Facile\Codec\Refiner;

/**
 * @implements Refiner<array<array-key,mixed>>
 */
class MapRefiner implements Refiner
{
    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true array<array-key,mixed> $u
     */
    public function is($u): bool
    {
        return is_array($u);
    }
}
