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
    /** @var A */
    private $value;

    /**
     * @psalm-param A $a
     *
     * @param mixed $a
     */
    public function __construct($a)
    {
        $this->value = $a;
    }

    /**
     * @psalm-return A
     */
    public function getValue()
    {
        return $this->value;
    }
}
