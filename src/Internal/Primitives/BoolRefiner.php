<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Refiner;

/**
 * @implements Refiner<bool>
 */
class BoolRefiner implements Refiner
{
    public function is($u): bool
    {
        return is_bool($u);
    }
}
