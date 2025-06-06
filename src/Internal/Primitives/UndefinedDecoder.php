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
 */
final class UndefinedDecoder implements Decoder
{
    /** @var U */
    private $default;

    /**
     * @psalm-param U $default
     *
     * @param mixed $default
     */
    public function __construct($default)
    {
        $this->default = $default;
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
