<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generator as g;
use Eris\Generators;

final class GeneratorUtils
{
    /**
     * @psalm-suppress MixedInferredReturnType
     */
    public static function scalar(): g
    {
        /**
         * @psalm-suppress UndefinedFunction
         * @psalm-suppress MixedReturnStatement
         */
        return Generators::oneOf(
            Generators::int(),
            Generators::float(),
            Generators::bool(),
            Generators::string()
        );
    }
}
