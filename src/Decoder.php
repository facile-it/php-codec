<?php declare(strict_types=1);

namespace Facile\Codec;

use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;

/**
 * @template I
 * @template A
 */
interface Decoder
{
    /**
     * @param I $i
     * @param Context $context
     * @return Validation<A>
     */
    public function validate($i, Context $context): Validation;

    /**
     * @param I $i
     * @return Validation<A>
     */
    public function decode($i): Validation;

    public function getName(): string;
}
