<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generator as g;
use Eris\Generators;

final class GeneratorUtils
{
    public static function scalar(): g
    {
        return Generators::oneOf(
            Generators::int(),
            Generators::float(),
            Generators::bool(),
            Generators::string()
        );
    }
}
