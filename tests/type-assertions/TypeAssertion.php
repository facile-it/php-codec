<?php

declare(strict_types=1);

namespace TypeAssertions\Facile\PhpCodec;

class TypeAssertion
{
    /**
     * @return mixed
     */
    protected static function mixed()
    {
        return null;
    }

    /**
     * @psalm-param true $b
     */
    protected static function assertTrue(bool $b): void
    {
    }

    /**
     * @psalm-param false $b
     */
    protected static function assertFalse(bool $b): void
    {
    }

    protected static function assertBool(bool $b): void
    {
    }

    protected static function assertString(string $s): void
    {
    }

    protected static function assertInt(int $i): void
    {
    }
}
