<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

final class Undefined
{
    public function __toString(): string
    {
        return 'undefined';
    }
}
