<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\DecodersTest;

class A
{
    public function __construct(private readonly int $v) {}

    public function getValue(): int
    {
        return $this->v;
    }
}
