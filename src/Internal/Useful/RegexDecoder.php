<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template-implements Decoder<string, string[]>
 */
final class RegexDecoder implements Decoder
{
    /** @var string */
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    public function validate($i, Context $context): Validation
    {
        /** @var string[] $matches */
        $matches = [];
        if (\preg_match($this->regex, $i, $matches) === false) {
            return Validation::failure($i, $context);
        }

        return Validation::success($matches);
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('regex(%s)', $this->regex);
    }
}
