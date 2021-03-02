<?php declare(strict_types=1);

namespace Pybatt\Codec\CommonTypes;

use Pybatt\Codec\Context;
use Pybatt\Codec\ContextEntry;
use Pybatt\Codec\Encode;
use Pybatt\Codec\Refiners\RefineUnion;
use Pybatt\Codec\Type;
use Pybatt\Codec\Validation;

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
            new RefineUnion($typeA, $typeB),
            Encode::identity()
        );
        $this->typeA = $typeA;
        $this->typeB = $typeB;
    }

    public function validate($i, Context $context): Validation
    {
        if($this->typeA->is($i)) {
            return $this->typeA->validate($i, $context);
        }

        if($this->typeB->is($i)) {
            return $this->typeB->validate($i, $context);
        }

        $context = $context->appendEntries(
            new ContextEntry('', $this, $i)
        );

        return Validation::failure($i, $context);
    }
}
