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
 * @implements Decoder<A, B>
 * @psalm-internal Facile\PhpCodec
 */
final class MapDecoder implements Decoder
{
    /** @var callable(A):B */
    private $f;
    /** @var string */
    private $name;

    /**
     * @psalm-param callable(A):B $f
     *
     * @param callable $f
     * @param string   $name
     */
    public function __construct(callable $f, string $name = 'map')
    {
        $this->f = $f;
        $this->name = $name;
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
