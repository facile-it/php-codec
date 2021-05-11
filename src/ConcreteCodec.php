<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 * @template I
 * @template O
 *
 * @implements Codec<A, I, O>
 */
final class ConcreteCodec implements Codec
{
    /** @var Decoder<I, A> */
    private $decoder;
    /** @var Encoder<A, O> */
    private $encoder;

    /**
     * @psalm-param Decoder<I, A> $decoder
     * @psalm-param Encoder<A, O> $encoder
     */
    public function __construct(Decoder $decoder, Encoder $encoder)
    {
        $this->decoder = $decoder;
        $this->encoder = $encoder;
    }

    public function validate($i, Context $context): Validation
    {
        return $this->decoder->validate($i, $context);
    }

    public function decode($i): Validation
    {
        return $this->decoder->decode($i);
    }

    public function getName(): string
    {
        return $this->decoder->getName();
    }

    public function encode($a)
    {
        return $this->encoder->encode($a);
    }
}
