<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Refiner;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

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
     * @psalm-param string       $name
     * @psalm-param Refiner<A>   $refine
     * @psalm-param Encode<A, O> $encode
     */
    public function __construct(
        string $name,
        Refiner $refine,
        Encode $encode
    ) {
        $this->name = $name;
        $this->encode = $encode;
        $this->refine = $refine;
    }

    /**
     * @param mixed $u
     * @psalm-assert-if-true A $i
     *
     * @return bool
     */
    final public function is($u): bool
    {
        return $this->refine->is($u);
    }

    /**
     * @psalm-param I $i
     * @psalm-return Validation<A>
     *
     * @param mixed $i
     */
    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    /**
     * @psalm-param I $i
     * @psalm-return Validation<A>
     *
     * @param mixed $i
     */
    abstract public function validate($i, Context $context): Validation;

    /**
     * @psalm-param A $a
     * @psalm-return O
     *
     * @param mixed $a
     */
    public function encode($a)
    {
        return ($this->encode)($a);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
