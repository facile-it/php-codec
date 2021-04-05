<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @template A
 */
interface Refiner
{
    /**
     * @param mixed $u
     * @psalm-assert-if-true A $u
     *
     * @return bool
     */
    public function is($u): bool;
}
