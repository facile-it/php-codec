<?php declare(strict_types=1);

namespace Pybatt\Codec\Refiners;

use Pybatt\Codec\Refine;

/**
 * @template A
 * @template B
 * @implements Refine<A | B>
 */
class RefineUnion implements Refine
{
    /** @var Refine */
    private $refineA;
    /** @var Refine */
    private $refineB;

    /**
     * @param Refine<A> $refineA
     * @param Refine<B> $refineB
     */
    public function __construct(
        Refine $refineA,
        Refine $refineB
    )
    {
        $this->refineA = $refineA;
        $this->refineB = $refineB;
    }

    public function is($u): bool
    {
        foreach ([$this->refineA, $this->refineB] as $refine) {
            if($refine->is($u)) {
                return true;
            }
        }

        return false;
    }
}
