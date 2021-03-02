<?php declare(strict_types=1);

namespace Pybatt\Codec\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<null>
 */
class RefineNull implements Refine {
    public function is($u): bool
    {
        return $u === null;
    }
}
