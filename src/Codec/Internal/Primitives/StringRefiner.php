<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refiner;

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
