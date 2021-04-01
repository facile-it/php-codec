<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Arrays;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;

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
        foreach ($i as $item) {
            $validation[] = $this->itemCodec->validate($item, $context);
        }

        return Validation::sequence($validation);
    }
}
