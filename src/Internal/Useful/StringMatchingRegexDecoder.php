<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template-implements Decoder<string, string>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class StringMatchingRegexDecoder implements Decoder
{
    public function __construct(private readonly string $regex) {}

    public function validate($i, Context $context): Validation
    {
        if (! \is_string($i)) {
            return Validation::failure($i, $context);
        }

        if (\preg_match($this->regex, $i)) {
            return Validation::success($i);
        }

        return Validation::failure($i, $context);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('stringMatchingRegex(%s)', $this->regex);
    }
}
