<?php declare(strict_types=1);

namespace Pybatt\Codec;

/**
 * @template A
 * @template I
 * @template O
 *
 * @implements Decoder<I, A>
 * @implements Encoder<A, O>
 * @implements Refine<A>
 */
abstract class Type implements Decoder, Encoder, Refine
{
    /** @var string */
    protected $name;
    /** @var Encode<A,O> */
    protected $encode;
    /** @var Refine */
    private $refine;

    /**
     * @param string $name
     * @param Refine<A> $refine
     * @param Encode<A, O> $encode
     */
    public function __construct(
        string $name,
        Refine $refine,
        Encode $encode
    )
    {
        $this->name = $name;
        $this->encode = $encode;
        $this->refine = $refine;
    }

    /**
     * @param I $u
     * @return bool
     * @psalm-assert-if-true A $i
     */
    final public function is($u): bool {
        return $this->refine->is($u);
    }

    public function decode($i): Validation
    {
        return $this->validate(
            $i,
            new Context(
                new ContextEntry('', $this, $i)
            )
        );
    }

    public function encode($a)
    {
        return ($this->encode)($a);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
