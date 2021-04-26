<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Refiner;

/**
 * @implements Refiner<callable>
 */
class CallableRefiner implements Refiner
{
    public function is($u): bool
    {
        return \is_callable($u);
    }
}
