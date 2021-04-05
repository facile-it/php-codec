<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationSuccess;

/**
 * @template A
 * @template B
 * @implements Codec<A|B, mixed, A|B>
 */
class UnionCodec implements Codec
{
    /** @var Codec<A, mixed, A> */
    private $a;
    /** @var Codec<B, mixed, B> */
    private $b;

    /**
     * @param Codec<A, mixed, A> $a
     * @param Codec<B, mixed, B> $b
     */
    public function __construct(
        Codec $a,
        Codec $b
    ) {
        $this->a = $a;
        $this->b = $b;
    }

    public function validate($i, Context $context): Validation
    {
        $va = $this->a->validate($i, $context);

        if ($va instanceof ValidationSuccess) {
            /** @var ValidationSuccess<A> */
            return $va;
        }

        return $this->b->validate($i, $context);
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return \sprintf('%s | %s', $this->a->getName(), $this->b->getName());
    }

    public function encode($a)
    {
        return $a;
    }
}
