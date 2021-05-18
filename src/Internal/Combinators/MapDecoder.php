<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 * @template B
 * @implements Decoder<A, B>
 */
class MapDecoder implements Decoder
{
    /** @var callable(A):B */
    private $f;

    /**
     * @psalm-param callable(A):B $f
     *
     * @param callable $f
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    public function validate($i, Context $context): Validation
    {
        return Validation::success(($this->f)($i));
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'map';
    }
}
