<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\ListOfValidation;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;

/**
 * @psalm-template I
 * @psalm-template A
 * @psalm-template B
 * @template-implements Decoder<I, A & B>
 * @psalm-internal Facile\PhpCodec
 */
final class IntersectionDecoder implements Decoder
{
    /** @var Decoder<I, A> */
    private $a;
    /** @var Decoder<I, B> */
    private $b;

    /**
     * @psalm-param Decoder<I, A> $a
     * @psalm-param Decoder<I, B> $b
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
                array_merge(
                    $va->getErrors(),
                    $vb->getErrors()
                )
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

        return Validation::map(
            FunctionUtils::destructureIn(
                /**
                 * @psalm-param A $a
                 * @psalm-param B $b
                 * @psalm-return A&B
                 *
                 * @param mixed $a
                 * @param mixed $b
                 */
                function ($a, $b) {
                    return self::intersectResults($a, $b);
                }
            ),
            ListOfValidation::sequence([$va, $vb])
        );
    }

    public function decode($i): Validation
    {
        /** @psalm-var Validation<A & B> */
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s & %s', $this->a->getName(), $this->b->getName());
    }

    /**
     * @template T1
     * @template T2
     * @psalm-param T1 $a
     * @psalm-param T2 $b
     * @psalm-return T1&T2
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return array|object
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
