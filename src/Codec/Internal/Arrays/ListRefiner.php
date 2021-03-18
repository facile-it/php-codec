<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Arrays;

use Pybatt\Codec\Refiner;

/**
 * @template T
 * @implements Refiner<list<T>>
 */
class ListRefiner implements Refiner
{
    /** @var Refiner<T> */
    private $itemRefiner;

    /**
     * @param Refiner<T> $item
     */
    public function __construct(Refiner $item)
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
