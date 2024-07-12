<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template U
 *
 * @template-implements Decoder<mixed, U>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class UndefinedDecoder implements Decoder
{
    /**
     * @psalm-param U $default
     */
    public function __construct(private readonly mixed $default)
    {
    }

    public function validate($i, Context $context): Validation
    {
        return $i instanceof Undefined
            ? Validation::success($this->default)
            : Validation::failure($i, $context);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'undefined';
    }
}
