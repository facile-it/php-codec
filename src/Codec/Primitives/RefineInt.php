<?php declare(strict_types=1);

namespace Pybatt\Codec\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<int>
 */
class RefineInt implements Refine
{
    public function is($u): bool
    {
        return is_int($u);
    }
}
