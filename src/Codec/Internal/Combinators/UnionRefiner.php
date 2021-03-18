<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Combinators;

use Pybatt\Codec\Refiner;

/**
 * @template A
 * @template B
 * @implements Refiner<A|B>
 */
class UnionRefiner implements Refiner
{
    /** @var Refiner<A> */
    private $a;
    /** @var Refiner<B> */
    private $b;

    /**
     * @param Refiner<A> $a
     * @param Refiner<B> $b
     */
    public function __construct(Refiner $a, Refiner $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function is($u): bool
    {
        return $this->a->is($u) || $this->b->is($u);
    }
}
