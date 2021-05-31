<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

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
        return Validation::bind(
            /**
             * @psalm-param A $a
             * @psalm-return Validation<A & B>
             *
             * @param mixed $a
             */
            function ($a) use ($i): Validation {
                /** @psalm-var Closure(B):A&B $f */
                $f = $this->curryIntersect($a);

                return Validation::map(
                    $f,
                    $this->b->decode($i)
                );
            },
            $this->a->decode($i)
        );
    }

    public function decode($i): Validation
    {
        /** @psalm-var Validation<A & B> */
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s & %s', $this->a->getName(), $this->b->getName());
    }

    /**
     * @psalm-param A $a
     * @psalm-return Closure(B):A&B
     *
     * @param mixed $a
     */
    private function curryIntersect($a): \Closure
    {
        $f =
            /**
             * @psalm-param B $b
             * @psalm-return A&B
             *
             * @param mixed $b
             */
            static function ($b) use ($a) {
                if (\is_array($a) && \is_array($b)) {
                    /** @var A&B */
                    return \array_merge($a, $b);
                }

                if ($a instanceof \stdClass && $b instanceof \stdClass) {
                    /** @var A&B */
                    return (object) \array_merge((array) $a, (array) $b);
                }

                /**
                 * @psalm-var A&B $b
                 */
                return $b;
            };

        return $f;
    }
}
