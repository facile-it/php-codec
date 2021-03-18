<?php declare(strict_types=1);

namespace Pybatt\Codec;

/**
 * @template A
 */
interface Refiner
{
    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true A $u
     */
    public function is($u): bool;
}
