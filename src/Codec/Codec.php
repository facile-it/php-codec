<?php declare(strict_types=1);

namespace Pybatt\Codec;

/**
 * @template A
 * @template I
 * @template O
 *
 * @implements Decoder<I, A>
 * @implements Encoder<A, O>
 * @implements Refiner<A>
 */
interface Codec extends Decoder, Encoder, Refiner
{
    /**
     * @param mixed $i
     * @return static
     * @psalm-assert I $i
     */
    public function forceCheckPrecondition($i);
}
