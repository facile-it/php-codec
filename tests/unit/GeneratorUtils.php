<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generator as g;

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
        return g\oneOf(
            g\int(),
            g\float(),
            g\bool(),
            g\string()
        );
    }
}
