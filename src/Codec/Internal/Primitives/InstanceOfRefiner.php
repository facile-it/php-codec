<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refiner;

/**
 * @template T of object
 * @implements Refiner<T>
 */
class InstanceOfRefiner implements Refiner
{
    /** @var class-string<T> */
    private $fqcn;

    /**
     * @param class-string<T> $fqcn
     */
    public function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
    }

    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true T $u
     */
    public function is($u): bool
    {
        return is_a($u, $this->fqcn);
    }
}
