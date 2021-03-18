<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal;

use Pybatt\Codec\Codec;
use Pybatt\Codec\Refiner;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\ContextEntry;
use Pybatt\Codec\Validation\Validation;

/**
 * @template A
 * @template I
 * @template O
 *
 * @implements Codec<A, I, O>
 */
abstract class Type implements Codec
{
    /** @var string */
    protected $name;
    /** @var Encode<A,O> */
    protected $encode;
    /** @var Refiner */
    private $refine;

    /**
     * @param string $name
     * @param Refiner<A> $refine
     * @param Encode<A, O> $encode
     */
    public function __construct(
        string $name,
        Refiner $refine,
        Encode $encode
    )
    {
        $this->name = $name;
        $this->encode = $encode;
        $this->refine = $refine;
    }

    /**
     * @param mixed $u
     * @return bool
     * @psalm-assert-if-true A $i
     */
    final public function is($u): bool {
        return $this->refine->is($u);
    }

    /**
     * @param I $i
     * @param Context $context
     * @return Validation<A>
     */
    public function decode($i): Validation
    {
        return $this->validate(
            $i,
            new Context(
                new ContextEntry('', $this, $i)
            )
        );
    }

    /**
     * @param I $i
     * @param Context $context
     * @return Validation<A>
     */
    abstract public function validate($i, Context $context): Validation;

    /**
     * @param A $a
     * @return O
     */
    public function encode($a)
    {
        return ($this->encode)($a);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $i
     * @return static
     * @psalm-assert I $i
     */
    public function forceCheckPrecondition($i)
    {
        return $this;
    }
}
