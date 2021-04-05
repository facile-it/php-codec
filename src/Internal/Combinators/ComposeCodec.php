<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 * @template IA
 * @template OA
 * @template B
 * @template OB
 *
 * Codec<A, IA, OA>
 * Codec<B, A, OB>
 *
 * @implements Codec<B, IA, OB>
 */
class ComposeCodec implements Codec
{
    /** @var Codec<A, IA, OA> */
    private $a;
    /** @var Codec<B, A, OB> */
    private $b;

    /**
     * @param Codec<A, IA, OA> $a
     * @param Codec<B, A, OB>  $b
     */
    public function __construct(
        Codec $a,
        Codec $b
    ) {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @param IA      $i
     * @param Context $context
     *
     * @return Validation<B>
     */
    public function validate($i, Context $context): Validation
    {
        return Validation::bind(
            /**
             * @param A $aValue
             */
            function ($aValue) use ($context): Validation {
                return $this->b->validate($aValue, $context);
            },
            $this->a->validate($i, $context)
        );
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return $this->b->getName();
    }

    public function encode($a)
    {
        return $this->b->encode($a);
    }
}
