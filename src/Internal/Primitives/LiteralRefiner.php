<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Refiner;

/**
 * @template T of bool | int | string
 * @implements Refiner<T>
 */
class LiteralRefiner implements Refiner
{
    /** @var T */
    private $literal;

    /**
     * @param T $literal
     */
    public function __construct($literal)
    {
        $this->literal = $literal;
    }

    /**
     * @param mixed $u
     * @psalm-assert-if-true T $u
     */
    public function is($u): bool
    {
        return $u === $this->literal;
    }
}
