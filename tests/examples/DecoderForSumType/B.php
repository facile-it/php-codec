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

    private int $case;
    private float $amount;
    private bool $flag;

    public function __construct(int $case, float $amount, bool $flag)
    {
        $this->case = $case;
        $this->amount = $amount;
        $this->flag = $flag;
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
