<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

use Facile\PhpCodec\Encoder;

/**
 * @psalm-template T
 *
 * @implements Encoder<T, T>
 */
final class IdentityEncoder implements Encoder
{
    public function encode($a)
    {
        return $a;
    }
}
