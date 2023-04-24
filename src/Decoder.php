<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template I
 * @psalm-template A
 */
interface Decoder
{
    /**
     * @psalm-param I $i
     * @psalm-param Context $context
     * @psalm-return Validation<A>
     *
     * @param mixed $i
     */
    public function validate($i, Context $context): Validation;

    /**
     * @psalm-param I $i
     * @psalm-return Validation<A>
     *
     * @param mixed $i
     */
    public function decode($i): Validation;

    public function getName(): string;
}
