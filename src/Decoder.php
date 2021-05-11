<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template I
 * @template A
 */
interface Decoder
{
    /**
     * @psalm-param I $i
     * @psalm-return Validation<A>
     *
     * @param mixed   $i
     * @param Context $context
     *
     * @return Validation
     */
    public function validate($i, Context $context): Validation;

    /**
     * @psalm-param I $i
     * @psalm-return Validation<A>
     *
     * @param mixed $i
     *
     * @return Validation
     */
    public function decode($i): Validation;

    public function getName(): string;
}
