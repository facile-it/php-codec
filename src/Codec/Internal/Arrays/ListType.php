<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Arrays;

use Pybatt\Codec\Encode;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\ContextEntry;
use Pybatt\Codec\Validation\Validation;

/**
 * @template T
 *
 * @extends Type<list<T>, mixed, list<T>>
 */
class ListType extends Type
{
    /** @var Type<T, mixed, T> */
    private $itemType;

    /**
     * @param Type<T, mixed, T> $itemType
     */
    public function __construct(Type $itemType)
    {
        parent::__construct(
            $itemType->getName() . '[]',
            new ListRefine($itemType),
            Encode::identity()
        );
        $this->itemType = $itemType;
    }

    public function validate($i, Context $context): Validation
    {
        if (!is_array($i)) {
            return Validation::failure(
                $i,
                $context->appendEntries(
                    new ContextEntry(
                        $this->getName(),
                        $this->itemType,
                        $i
                    )
                )
            );
        }

        /** @var list<Validation<T>> $validation */
        $validation = [];
        /**
         * @var mixed $item
         */
        foreach ($i as $k => $item) {
            $validation[] = $this->itemType->validate($item, $context);
        }

        return Validation::sequence($validation);
    }
}
