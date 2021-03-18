<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refiner;

/**
 * @implements Refiner<null>
 */
class NullRefiner implements Refiner {
    public function is($u): bool
    {
        return $u === null;
    }
}
