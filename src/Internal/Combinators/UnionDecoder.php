<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
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
    /** @var int */
    private $indexBegin;

    /**
     * @psalm-param Decoder<IA, A> $a
     * @psalm-param Decoder<IB, B> $b
     */
    public function __construct(
        Decoder $a,
        Decoder $b,
        int $indexBegin = 0
    ) {
        $this->a = $a;
        $this->b = $b;
        $this->indexBegin = $indexBegin;
    }

    public function validate($i, Context $context): Validation
    {
        $contextA = $context->appendEntries(new ContextEntry((string) $this->indexBegin, $this->a, $i));
        $va = $this->a->validate($i, $contextA);

        if ($va instanceof ValidationFailures) {
            $contextB = $context->appendEntries(new ContextEntry((string) ($this->indexBegin + 1), $this->b, $i));
            $vb = $this->b->validate($i, $this->b instanceof self ? $context : $contextB);

            if ($vb instanceof ValidationFailures) {
                return Validation::failures(
                    array_merge(
                        $va->getErrors(),
                        $vb->getErrors()
                    )
                );
            }

            return $vb;
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
