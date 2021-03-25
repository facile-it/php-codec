<?php declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @template A
 * @template O
 */
interface Encoder
{
    /**
     * @param A $a
     * @return O
     */
    public function encode($a);
}
