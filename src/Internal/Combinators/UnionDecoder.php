<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
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
    private \Facile\PhpCodec\Decoder $a;
    /** @var Decoder<IB, B> */
    private \Facile\PhpCodec\Decoder $b;
    private int $indexBegin;

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
        $va = $this->a->validate(
            $i,
            $context->appendEntries(
                new ContextEntry(
                    (string) $this->indexBegin,
                    $this->a,
                    $i
                )
            )
        );

        if ($va instanceof ValidationFailures) {
            $vb = $this->b->validate(
                $i,
                $this->b instanceof self
                    ? $context
                    : $context->appendEntries(
                        new ContextEntry(
                            (string) ($this->indexBegin + 1),
                            $this->b,
                            $i
                        )
                    )
            );

            if ($vb instanceof ValidationFailures) {
                return Validation::failures(
                    [...$va->getErrors(), ...$vb->getErrors()]
                );
            }

            return $vb;
        }

        return $va;
    }

    public function decode($i): Validation
    {
        /**
         * @psalm-var IA&IB $i
         * @psalm-var Decoder<IA&IB, A|B> $this
         */

        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s | %s', $this->a->getName(), $this->b->getName());
    }
}
