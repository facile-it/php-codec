<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generator as g;

final class GeneratorUtils
{
    public static function scalar(): g
    {
        return g\oneOf(
            g\int(),
            g\float(),
            g\bool(),
            g\string()
        );
    }
}
