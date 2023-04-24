<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Utils;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template I
 * @template A
 *
 * @implements Decoder<I, A>
 */
final class ConcreteDecoder implements Decoder
{
    /** @var callable(I, Context):Validation<A> */
    private $validateFunc;
    private string $name;

    /**
     * @psalm-param callable(I, Context):Validation<A> $validate
     *
     * @param callable $validate
     * @param string   $name
     */
    public function __construct(
        callable $validate,
        string $name
    ) {
        $this->validateFunc = $validate;
        $this->name = $name;
    }

    public function validate($i, Context $context): Validation
    {
        return ($this->validateFunc)($i, $context);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
