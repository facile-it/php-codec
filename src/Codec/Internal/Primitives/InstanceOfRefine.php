<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Primitives;

use Pybatt\Codec\Refine;

/**
 * @template T of object
 * @implements Refine<T>
 */
class InstanceOfRefine implements Refine
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
