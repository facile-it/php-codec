<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationFailures;

/**
 * @psalm-template IA
 * @psalm-template IB
 * @psalm-template A
 * @psalm-template B
 * @template-implements Decoder<IA & IB, A | B>
 * @psalm-internal Facile\PhpCodec
 */
final class UnionDecoder implements Decoder
{
    /** @var Decoder<IA, A> */
    private $a;
    /** @var Decoder<IB, B> */
    private $b;

    /**
     * @psalm-param Decoder<IA, A> $a
     * @psalm-param Decoder<IB, B> $b
     */
    public function __construct(
        Decoder $a,
        Decoder $b
    ) {
        $this->a = $a;
        $this->b = $b;
    }

    public function validate($i, Context $context): Validation
    {
        $va = $this->a->validate($i, $context);

        if ($va instanceof ValidationFailures) {
            return $this->b->validate($i, $context);
        }

        return $va;
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s | %s', $this->a->getName(), $this->b->getName());
    }
}
