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
 * @template I
 * @template A
 * @template B
 *
 * @implements Decoder<I, A | B>
 */
final class UnionDecoder implements Decoder
{
    /**
     * @param Decoder<I, A> $a
     * @param Decoder<I, B> $b
     */
    public function __construct(
        private readonly \Facile\PhpCodec\Decoder $a,
        private readonly \Facile\PhpCodec\Decoder $b,
        private readonly int $indexBegin = 0
    ) {}

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
                /** @var Validation<A|B> $validationFailures */
                $validationFailures = Validation::failures(
                    [...$va->getErrors(), ...$vb->getErrors()]
                );

                return $validationFailures;
            }

            /** @var Validation<A|B> $vb */
            return $vb;
        }

        /** @var Validation<A|B> $va */
        return $va;
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s | %s', $this->a->getName(), $this->b->getName());
    }
}
