<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Arrays;

use Facile\Codec\Codec;
use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\ContextEntry;
use Facile\Codec\Validation\Validation;

/**
 * @template T
 *
 * @extends Type<list<T>, mixed, list<T>>
 */
class ListType extends Type
{
    /** @var Codec<T, mixed, T> */
    private $itemCodec;

    /**
     * @param Codec<T, mixed, T> $itemCodec
     */
    public function __construct(Codec $itemCodec)
    {
        parent::__construct(
            $itemCodec->getName() . '[]',
            new ListRefiner($itemCodec),
            Encode::identity()
        );
        $this->itemCodec = $itemCodec;
    }

    public function validate($i, Context $context): Validation
    {
        if (!is_array($i)) {
            return Validation::failure(
                $i,
                $context->appendEntries(
                    new ContextEntry(
                        $this->getName(),
                        $this->itemCodec,
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
            $validation[] = $this->itemCodec->validate($item, $context);
        }

        return Validation::sequence($validation);
    }
}
