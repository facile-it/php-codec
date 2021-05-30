<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Refiner;

/**
 * @implements Refiner<null>
 *
 * @deprecated use decoder
 */
class NullRefiner implements Refiner
{
    public function is($u): bool
    {
        return $u === null;
    }
}
