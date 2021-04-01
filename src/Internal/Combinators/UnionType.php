<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationSuccess;

/**
 * @template A
 * @template B
 * @extends Type<A|B, mixed, A|B>
 */
class UnionType extends Type
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
        $name = sprintf(
            '%s | %s',
            $a->getName(),
            $b->getName()
        );

        parent::__construct(
            $name,
            new UnionRefiner($a, $b),
            Encode::identity()
        );
        $this->a = $a;
        $this->b = $b;
    }

    public function validate($i, Context $context): Validation
    {
        $va = $this->a
            ->forceCheckPrecondition($i)
            ->validate($i, $context);

        if ($va instanceof ValidationSuccess) {
            /** @var ValidationSuccess<A> */
            return $va;
        }

        return $this->b
            ->forceCheckPrecondition($i)
            ->validate($i, $context);
    }
}
