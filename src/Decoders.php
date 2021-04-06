<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;

final class Decoders
{
    private function __construct()
    {
    }

    /**
     * @template U
     *
     * @param U $default
     *
     * @return Decoder<mixed, U>
     */
    public static function undefined($default = null): Decoder
    {
        return new UndefinedDecoder($default);
    }
}
