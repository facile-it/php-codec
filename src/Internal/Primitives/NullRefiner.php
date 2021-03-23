<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Refiner;

/**
 * @implements Refiner<null>
 */
class NullRefiner implements Refiner {
    public function is($u): bool
    {
        return $u === null;
    }
}
