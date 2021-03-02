<?php declare(strict_types=1);

namespace Pybatt\Codec\Refiners;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<array<string, mixed>>
 */
class RefineAssociativeArrayWithStringKeys implements Refine
{
    /**
     * @var non-empty-array<string, Refine>
     */
    private $props;

    /**
     * @param non-empty-array<string, Refine> $props
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
