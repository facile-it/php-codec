<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<int>
 */
class IntRefine implements Refine
{
    public function is($u): bool
    {
        return is_int($u);
    }
}
