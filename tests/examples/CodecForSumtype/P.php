<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\CodecForSumtype;

/**
 * @psalm-internal Examples\Facile\PhpCodec\CodecForSumtype
 */
abstract class P
{
    public const Type_a = 'a';
    public const Type_b = 'b';

    abstract public function getType(): string;
}
