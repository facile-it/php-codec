<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @template A
 *
 * @extends Validation<A>
 */
final class ValidationSuccess extends Validation
{
    /**
     * @param A $value
     */
    public function __construct(private readonly mixed $value) {}

    /**
     * @psalm-return A
     */
    public function getValue()
    {
        return $this->value;
    }
}
