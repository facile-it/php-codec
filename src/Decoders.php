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
     * @psalm-param U $default
     * @psalm-return Decoder<mixed, U>
     *
     * @param null|mixed $default
     */
    public static function undefined($default = null): Decoder
    {
        return new UndefinedDecoder($default);
    }
}
