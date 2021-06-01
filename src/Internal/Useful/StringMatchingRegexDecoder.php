<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template-implements Decoder<string, string>
 */
final class StringMatchingRegexDecoder implements Decoder
{
    /** @var string */
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    public function validate($i, Context $context): Validation
    {
        /** @psalm-suppress DocblockTypeContradiction */
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
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('stringMatchingRegex(%s)', $this->regex);
    }
}
