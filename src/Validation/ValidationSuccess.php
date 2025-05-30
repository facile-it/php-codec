<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @psalm-template A
 *
 * @extends Validation<A>
 */
final class ValidationSuccess extends Validation
{
    /**
     * @psalm-param A $a
     *
     * @param mixed $value
     */
    public function __construct(private $value) {}

    /**
     * @psalm-return A
     */
    public function getValue()
    {
        return $this->value;
    }
}
