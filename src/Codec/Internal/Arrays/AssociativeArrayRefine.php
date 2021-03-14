<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Arrays;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<array<array-key, mixed>>
 */
class AssociativeArrayRefine implements Refine
{
    /** @var Refine[] */
    private $props;

    /**
     * @param non-empty-array<array-key, Refine> $props
     */
    public function __construct(array $props)
    {
        $this->props = $props;
    }

    public function is($u): bool
    {
        if(!is_array($u)) {
            return false;
        }

        foreach ($this->props as $key => $refiner) {
            if(! $refiner->is($u[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
