<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @template A
 * @template I
 * @template O
 *
 * @extends Decoder<I, A>
 * @extends Encoder<A, O>
 */
interface Codec extends Decoder, Encoder
{
}
