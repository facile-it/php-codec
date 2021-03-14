<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Combinators;

use Pybatt\Codec\Refine;

/**
 * @template A
 * @template B
 * @implements Refine<A|B>
 */
class UnionRefine implements Refine
{
    /** @var Refine<A> */
    private $a;
    /** @var Refine<B> */
    private $b;

    /**
     * @param Refine<A> $a
     * @param Refine<B> $b
     */
    public function __construct(Refine $a, Refine $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function is($u): bool
    {
        return $this->a->is($u) || $this->b->is($u);
    }
}
