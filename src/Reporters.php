<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Reporters\PathReporter;
use Facile\PhpCodec\Reporters\SimplePathReporter;

final class Reporters
{
    /**
     * @return Reporter<list<string>>
     */
    public static function path(): Reporter
    {
        return PathReporter::create();
    }

    /**
     * @return Reporter<list<string>>
     */
    public static function simplePath(): Reporter
    {
        return new SimplePathReporter();
    }
}
