<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refine;

/**
 * @template T of bool | int | string
 * @implements Refine<T>
 */
class LitteralRefine implements Refine
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
