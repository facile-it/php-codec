<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Refiner;

/**
 * @psalm-template T of bool | int | string
 * @implements Refiner<T>
 */
class LiteralRefiner implements Refiner
{
    /** @var T */
    private $literal;

    /**
     * @psalm-param T $literal
     *
     * @param mixed $literal
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
