<?php declare(strict_types=1);

namespace Pybatt\Codec\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<float>
 */
class RefineFloat implements Refine
{
    public function is($u): bool
    {
        return is_float($u);
    }
}
