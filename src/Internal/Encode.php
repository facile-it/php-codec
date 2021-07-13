<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

use const Facile\PhpCodec\identity;

/**
 * @psalm-template A
 * @psalm-template O
 */
final class Encode
{
    /** @var callable(A):O */
    private $encode;

    /**
     * @psalm-template X
     * @psalm-return Encode<X, X>
     */
    public static function identity(): self
    {
        return new self(identity);
    }

    /**
     * @psalm-param callable(A):O $encode
     */
    public function __construct(callable $encode)
    {
        $this->encode = $encode;
    }

    /**
     * @psalm-param A $a
     * @psalm-return O
     *
     * @param mixed $a
     */
    public function __invoke($a)
    {
        return ($this->encode)($a);
    }
}
