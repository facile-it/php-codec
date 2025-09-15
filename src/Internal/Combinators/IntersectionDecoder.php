<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;

/**
 * @psalm-template IA
 * @psalm-template IB
 * @psalm-template A
 * @psalm-template B
 *
 * @template-implements Decoder<IA & IB, A & B>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class IntersectionDecoder implements Decoder
{
    /** @var Decoder<IA, A> */
    private \Facile\PhpCodec\Decoder $a;
    /** @var Decoder<IB, B> */
    private \Facile\PhpCodec\Decoder $b;

    /**
     * @psalm-param Decoder<IA, A> $a
     * @psalm-param Decoder<IB, B> $b
     */
    public function __construct(Decoder $a, Decoder $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function validate($i, Context $context): Validation
    {
        /** @var Validation<A> $va */
        $va = $this->a->validate($i, $context->appendEntries(new ContextEntry('0', $this->a, $i)));
        /** @var Validation<B> $vb */
        $vb = $this->b->validate($i, $context->appendEntries(new ContextEntry('1', $this->b, $i)));

        if ($va instanceof ValidationFailures && $vb instanceof ValidationFailures) {
            return ValidationFailures::failures(
                [...$va->getErrors(), ...$vb->getErrors()]
            );
        }

        if ($va instanceof ValidationFailures && $vb instanceof ValidationSuccess) {
            /** @psalm-var Validation<A&B> $va */
            return $va;
        }

        if ($va instanceof ValidationSuccess && $vb instanceof ValidationFailures) {
            /** @psalm-var Validation<A&B> $vb */
            return $vb;
        }

        /**
         * @psalm-var ValidationSuccess<A> $va
         * @psalm-var ValidationSuccess<B> $vb
         */
        return ValidationSuccess::success(
            self::intersectResults(
                $va->getValue(),
                $vb->getValue()
            )
        );
    }

    public function decode($i): Validation
    {
        /**
         * @psalm-var IA & IB $i
         * @psalm-var Decoder<IA&IB, A&B> $this
         */
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s & %s', $this->a->getName(), $this->b->getName());
    }

    /**
     * @template T1
     * @template T2
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @psalm-param T1 $a
     * @psalm-param T2 $b
     *
     * @return array|object
     *
     * @psalm-return T1&T2
     */
    private static function intersectResults($a, $b)
    {
        if (\is_array($a) && \is_array($b)) {
            /** @var T1&T2 */
            return \array_merge($a, $b);
        }

        if ($a instanceof \stdClass && $b instanceof \stdClass) {
            /** @var T1&T2 */
            return (object) \array_merge((array) $a, (array) $b);
        }

        /**
         * @psalm-var T1&T2 $b
         */
        return $b;
    }
}
