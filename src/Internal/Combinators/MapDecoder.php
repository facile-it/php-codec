<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 * @template B
 *
 * @implements Decoder<A, B>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class MapDecoder implements Decoder
{
    /** @var callable(A):B */
    private $f;

    /**
     * @psalm-param callable(A):B $f
     */
    public function __construct(callable $f, private readonly string $name = 'map')
    {
        $this->f = $f;
    }

    public function validate($i, Context $context): Validation
    {
        return Validation::success(($this->f)($i));
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
