<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template-implements Decoder<mixed, int>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class BoundedIntDecoder implements Decoder
{
    /**
     * @psalm-readonly
     */
    private int $min;

    /**
     * @psalm-readonly
     */
    private int $max;

    public function __construct(int $min, int $max)
    {
        if ($min > $max) {
            throw new \InvalidArgumentException('Lower bound cannot be greater than upper bound.');
        }

        $this->min = $min;
        $this->max = $max;
    }

    public function validate($i, Context $context): Validation
    {
        if (! \is_int($i)) {
            return Validation::failure($i, $context);
        }

        if ($i < $this->min || $i > $this->max) {
            return Validation::failure($i, $context);
        }

        return Validation::success($i);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return sprintf('BoundedInt(%d, %d)', $this->min, $this->max);
    }
}
