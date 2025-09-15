<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template U of mixed
 *
 * @template-implements Decoder<U, U>
 */
final class MixedDecoder implements Decoder
{
    public function validate($i, Context $context): Validation
    {
        return Validation::success($i);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'mixed';
    }
}
