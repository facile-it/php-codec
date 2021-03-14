<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Arrays;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<array<array-key,mixed>>
 */
class MapRefine implements Refine
{
    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true array<array-key,mixed> $u
     */
    public function is($u): bool
    {
        return is_array($u);
    }
}
