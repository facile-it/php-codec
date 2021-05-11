<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @template A
 * @template O
 */
interface Encoder
{
    /**
     * @psalm-param A $a
     * @psalm-return O
     *
     * @param mixed $a
     *
     * @return mixed
     */
    public function encode($a);
}
