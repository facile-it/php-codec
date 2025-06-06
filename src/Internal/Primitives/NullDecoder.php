<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template I of mixed
 *
 * @template-implements Decoder<I, null>
 */
final class NullDecoder implements Decoder
{
    public function validate($i, Context $context): Validation
    {
        return $i === null
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'null';
    }
}
