<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @implements Decoder<string, int>
 * @psalm-internal Facile\PhpCodec
 */
final class IntFromStringDecoder implements Decoder
{
    public function validate($i, Context $context): Validation
    {
        return \is_numeric($i)
            ? Validation::success((int) $i)
            : Validation::failure($i, $context);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'IntFromString';
    }
}
