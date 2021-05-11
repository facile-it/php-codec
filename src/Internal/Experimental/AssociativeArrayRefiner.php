<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Experimental;

use Facile\PhpCodec\Refiner;

/**
 * @implements Refiner<array<array-key, mixed>>
 */
class AssociativeArrayRefiner implements Refiner
{
    /** @var Refiner[] */
    private $props;

    /**
     * @psalm-param non-empty-array<array-key, Refiner> $props
     */
    public function __construct(array $props)
    {
        $this->props = $props;
    }

    public function is($u): bool
    {
        if (! \is_array($u)) {
            return false;
        }

        foreach ($this->props as $key => $refiner) {
            if (! $refiner->is($u[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
