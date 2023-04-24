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

    /** @var string */
    private $subType;
    /** @var int */
    private $propertyA;
    /** @var string */
    private $propertyB;

    public function __construct(string $subType, int $propertyA, string $propertyB)
    {
        $this->subType = $subType;
        $this->propertyA = $propertyA;
        $this->propertyB = $propertyB;
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
