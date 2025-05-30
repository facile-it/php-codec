<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\ListOfValidation;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template K of array-key
 * @psalm-template Vs
 * @psalm-template I
 * @psalm-template PD of non-empty-array<K, Decoder<mixed, Vs>>
 *
 * @template-implements Decoder<I, non-empty-array<K, Vs>>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class ArrayPropsDecoder implements Decoder
{
    /**
     * @psalm-param PD $props
     */
    public function __construct(
        /** @var PD */
        private readonly array $props
    ) {}

    public function validate($i, Context $context): Validation
    {
        if (! \is_array($i)) {
            return Validation::failure($i, $context);
        }

        /**
         * @psalm-var non-empty-array<K, Validation<Vs>> $validations
         */
        $validations = [];

        foreach ($this->props as $k => $decoder) {
            $keyName = \is_string($k) ? $k : \sprintf('[%d]', $k);
            /** @var mixed $value */
            $value = \array_key_exists($k, $i) ? $i[$k] : new Undefined();

            $validations[$k] = $decoder->validate($value, $context->appendEntries(new ContextEntry($keyName, $decoder, $value)));
        }

        return ListOfValidation::reduceToIndexedSuccessOrAllFailures($validations);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return FunctionUtils::nameFromProps($this->props);
    }
}
