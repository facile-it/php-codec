<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 * @template IA
 * @template OA
 * @template B
 * @template OB
 *
 * Type<A, IA, OA>
 * Type<B, A, OB>
 *
 * @extends Type<B, IA, OB>
 */
class ComposeType extends Type
{
    /** @var Codec<A, IA, OA> */
    private $a;
    /** @var Codec<B, A, OB> */
    private $b;

    /**
     * @param Codec<A, IA, OA> $a
     * @param Codec<B, A, OB> $b
     */
    public function __construct(
        Codec $a,
        Codec $b
    ) {
        $this->a = $a;
        $this->b = $b;

        parent::__construct(
            $b->getName(),
            $b,
            Encode::fromCodec($b)
        );
    }

    /**
     * @param IA $i
     */
    public function validate($i, Context $context): Validation
    {
        return Validation::bind(
            /**
             * @param A $aValue
             */
            function ($aValue) use ($context): Validation {
                return $this->b->forceCheckPrecondition($aValue)->validate($aValue, $context);
            },
            $this->a->forceCheckPrecondition($i)->validate($i, $context)
        );
    }
}
