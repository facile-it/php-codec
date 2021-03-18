<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refiner;

/**
 * @template T of bool | int | string
 * @implements Refiner<T>
 */
class LitteralRefiner implements Refiner
{
    /** @var T */
    private $litteral;

    /**
     * @param T $litteral
     */
    public function __construct($litteral)
    {
        $this->litteral = $litteral;
    }

    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true T $u
     */
    public function is($u): bool
    {
        return $u === $this->litteral;
    }
}
