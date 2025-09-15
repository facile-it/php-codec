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
     * @param mixed $i
     *
     * @psalm-param I $i
     * @psalm-param Context $context
     *
     * @psalm-return Validation<A>
     */
    public function validate($i, Context $context): Validation;

    /**
     * @param mixed $i
     *
     * @psalm-param I $i
     *
     * @psalm-return Validation<A>
     */
    public function decode($i): Validation;

    public function getName(): string;
}
