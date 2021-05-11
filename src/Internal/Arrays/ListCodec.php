<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Arrays;

use Facile\PhpCodec\Codec;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template T
 *
 * @implements Codec<list<T>, mixed, list<T>>
 */
class ListCodec implements Codec
{
    /** @var Codec<T, mixed, T> */
    private $itemCodec;

    /**
     * @psalm-param Codec<T, mixed, T> $itemCodec
     *
     * @param Codec $itemCodec
     */
    public function __construct(Codec $itemCodec)
    {
        $this->itemCodec = $itemCodec;
    }

    public function validate($i, Context $context): Validation
    {
        if (! \is_array($i)) {
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

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return $this->itemCodec->getName() . '[]';
    }

    public function encode($a)
    {
        return $a;
    }
}
