<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template I of mixed
 * @template T of bool | string | int
 *
 * @template-implements Decoder<I, T>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class LiteralDecoder implements Decoder
{
    /**
     * @param T $literal
     */
    public function __construct(private readonly string|bool|int $literal) {}

    public function validate($i, Context $context): Validation
    {
        if ($this->literal === $i) {
            return Validation::success($this->literal);
        }

        return Validation::failure($i, $context);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return self::literalName($this->literal);
    }

    private static function literalName(string|bool|int $x): string
    {
        if (\is_string($x)) {
            return "'{$x}'";
        }

        if (\is_bool($x)) {
            return $x ? 'true' : 'false';
        }

        return (string) $x;
    }
}
