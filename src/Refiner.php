<?php declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @template A
 */
interface Refiner
{
    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true A $u
     */
    public function is($u): bool;
}
