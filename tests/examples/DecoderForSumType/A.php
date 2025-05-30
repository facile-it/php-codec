<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecoderForSumType;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecoderForSumType
 */
final class A extends P
{
    public const SUB_foo = 'foo';
    public const SUB_bar = 'bar';

    public function __construct(private readonly string $subType, private readonly int $propertyA, private readonly string $propertyB)
    {
    }

    public function getType(): string
    {
        return self::Type_a;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function getPropertyA(): int
    {
        return $this->propertyA;
    }

    public function getPropertyB(): string
    {
        return $this->propertyB;
    }
}
