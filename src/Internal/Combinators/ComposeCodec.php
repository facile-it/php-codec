<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template A
 * @psalm-template IA
 * @psalm-template OA
 * @psalm-template B
 * @psalm-template OB
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
     * @psalm-param Codec<A, IA, OA> $a
     * @psalm-param Codec<B, A, OB>  $b
     */
    public function __construct(
        Codec $a,
        Codec $b
    ) {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @psalm-param IA      $i
     * @psalm-param Context $context
     * @psalm-return Validation<B>
     *
     * @param mixed $i
     */
    public function validate($i, Context $context): Validation
    {
        return Validation::bind(
            /**
             * @psalm-param A $aValue
             *
             * @param mixed $aValue
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
