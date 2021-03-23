<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Refiner;

/**
 * @implements Refiner<string>
 */
class StringRefiner implements Refiner
{
    public function is($u): bool
    {
        return is_string($u);
    }
}
