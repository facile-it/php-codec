<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Refiner;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 * @template B
 * @template I
 * @template O
 *
 * @extends Type<B, I, O>
 */
class GenericCombinatorType extends Type
{
    /** @var callable(A):B */
    private $f;
    /** @var callable(B):A */
    private $g;
    /** @var Codec<A, I, O> */
    private $fa;

    /**
     * @param callable(A):B $f
     * @param callable(B):A $g
     * @param Codec<A, I, O> $fa
     */
    public function __construct(
        callable $f,
        callable $g,
        Codec $fa
    )
    {
        parent::__construct($fa->getName(), $fa, Encode::identity());

        $this->f = $f;
        $this->g = $g;
        $this->fa = $fa;
    }

    public function validate($i, Context $context): Validation
    {
        return Validation::map(
            $this->f,
            $this->fa->validate($i, $context)
        );
    }
}
