<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Arrays;

use Pybatt\Codec\Refine;

/**
 * @template T
 * @implements Refine<list<T>>
 */
class ListRefine implements Refine
{
    /** @var Refine<T> */
    private $itemRefiner;

    /**
     * @param Refine<T> $item
     */
    public function __construct(Refine $item)
    {
        $this->itemRefiner = $item;
    }

    public function is($u): bool
    {
        if(! is_array($u)) {
            return false;
        }

        foreach ($u as $item) {
            if (!$this->itemRefiner->is($item)) {
                return false;
            }
        }

        return true;
    }
}
