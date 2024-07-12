<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecoderForSumType;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecoderForSumType
 */
final class B extends P
{
    public const CASE_B1 = 1;
    public const CASE_B2 = 2;
    public const CASE_B3 = 3;

    public function __construct(private readonly int $case, private readonly float $amount, private readonly bool $flag)
    {
    }

    public function getType(): string
    {
        return self::Type_b;
    }

    public function getCase(): int
    {
        return $this->case;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function isFlag(): bool
    {
        return $this->flag;
    }
}
