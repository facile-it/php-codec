<?php declare(strict_types=1);

namespace Pybatt\Codec;

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
