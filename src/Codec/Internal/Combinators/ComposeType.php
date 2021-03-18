<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Combinators;

use Pybatt\Codec\Codec;
use Pybatt\Codec\Internal\Encode;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\Validation;

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
    )
    {
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
     * @param Context $context
     * @return Validation
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
