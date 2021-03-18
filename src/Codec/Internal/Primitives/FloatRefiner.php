<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refiner;

/**
 * @implements Refiner<float>
 */
class FloatRefiner implements Refiner
{
    public function is($u): bool
    {
        return is_float($u);
    }
}
