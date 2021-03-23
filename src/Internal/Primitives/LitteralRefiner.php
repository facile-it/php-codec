<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Refiner;

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
