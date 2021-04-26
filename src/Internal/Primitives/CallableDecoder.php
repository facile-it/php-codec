<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @implements Decoder<mixed, callable>
 */
class CallableDecoder implements Decoder
{
    public function validate($i, Context $context): Validation
    {
        return \is_callable($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'callable';
    }
}