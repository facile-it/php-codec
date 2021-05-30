<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Refiner;

/**
 * @implements Refiner<string>
 *
 * @deprecated
 */
class StringRefiner implements Refiner
{
    public function is($u): bool
    {
        return \is_string($u);
    }
}
