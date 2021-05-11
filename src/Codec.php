<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @psalm-template A
 * @psalm-template I
 * @psalm-template O
 *
 * @extends Decoder<I, A>
 * @extends Encoder<A, O>
 */
interface Codec extends Decoder, Encoder
{
}
