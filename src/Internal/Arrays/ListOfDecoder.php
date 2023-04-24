<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Arrays;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\ListOfValidation;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template I of mixed
 * @psalm-template IT of mixed
 * @psalm-template T
 *
 * @template-implements Decoder<I, list<T>>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class ListOfDecoder implements Decoder
{
    /** @var Decoder<IT, T> */
    private \Facile\PhpCodec\Decoder $elementDecoder;

    /**
     * @psalm-param Decoder<IT, T> $elementDecoder
     */
    public function __construct(Decoder $elementDecoder)
    {
        $this->elementDecoder = $elementDecoder;
    }

    public function validate($i, Context $context): Validation
    {
        if (! \is_array($i)) {
            return Validation::failure(
                $i,
                $context->appendEntries(
                    new ContextEntry(
                        $this->getName(),
                        $this->elementDecoder,
                        $i
                    )
                )
            );
        }

        /** @var list<Validation<T>> $validation */
        $validation = [];

        /** @var IT $item */
        foreach ($i as $index => $item) {
            $validation[] = $this->elementDecoder->validate(
                $item,
                $context->appendEntries(
                    new ContextEntry(
                        (string) $index,
                        $this->elementDecoder,
                        $item
                    )
                )
            );
        }

        return ListOfValidation::sequence($validation);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return $this->elementDecoder->getName() . '[]';
    }
}
