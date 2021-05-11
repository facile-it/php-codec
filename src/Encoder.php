<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @psalm-template A
 * @psalm-template O
 */
interface Encoder
{
    /**
     * @psalm-param A $a
     * @psalm-return O
     *
     * @param mixed $a
     */
    public function encode($a);
}
