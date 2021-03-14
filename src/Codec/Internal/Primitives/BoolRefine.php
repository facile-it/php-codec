<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<bool>
 */
class BoolRefine implements Refine
{
    public function is($u): bool
    {
        return is_bool($u);
    }
}
