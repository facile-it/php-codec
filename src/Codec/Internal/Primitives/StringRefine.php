<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<string>
 */
class StringRefine implements Refine
{
    public function is($u): bool
    {
        return is_string($u);
    }
}
