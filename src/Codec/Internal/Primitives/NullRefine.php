<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<null>
 */
class NullRefine implements Refine {
    public function is($u): bool
    {
        return $u === null;
    }
}
