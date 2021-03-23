<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Refiner;

/**
 * @implements Refiner<int>
 */
class IntRefiner implements Refiner
{
    public function is($u): bool
    {
        return is_int($u);
    }
}
