<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Combinators;

use Pybatt\Codec\Encode;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\Validation;
use Pybatt\Codec\Validation\ValidationSuccess;

/**
 * @template A
 * @template B
 * @extends Type<A|B, mixed, A|B>
 */
class UnionType extends Type
{
    /** @var Type<A, mixed, A> */
    private $typeA;
    /** @var Type<B, mixed, B> */
    private $typeB;

    /**
     * @param Type<A, mixed, A> $typeA
     * @param Type<B, mixed, B> $typeB
     */
    public function __construct(
        Type $typeA,
        Type $typeB
    )
    {
        $name = sprintf(
            '%s | %s',
            $typeA->getName(),
            $typeB->getName()
        );

        parent::__construct(
            $name,
            new UnionRefine($typeA, $typeB),
            Encode::identity()
        );
        $this->typeA = $typeA;
        $this->typeB = $typeB;
    }

    public function validate($i, Context $context): Validation
    {
        $va = $this->typeA
            ->forceCheckPrecondition($i)
            ->validate($i, $context);

        if($va instanceof ValidationSuccess) {
            /** @var ValidationSuccess<A> */
            return $va;
        }

        return $this->typeB
            ->forceCheckPrecondition($i)
            ->validate($i, $context);
    }
}
