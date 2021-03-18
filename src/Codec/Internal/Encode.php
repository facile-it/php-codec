<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal;

use Pybatt\Codec\Codec;
use SebastianBergmann\CodeCoverage\Driver\Xdebug;
use const Pybatt\Codec\identity;

/**
 * @template A
 * @template O
 */
class Encode
{
    /** @var callable(A):O $encode */
    private $encode;

    /**
     * @template X
     * @return Encode<X, X>
     */
    public static function identity(): self
    {
        return new self(identity);
    }

    /**
     * @template X
     * @template Y
     * @template Z
     *
     * @param Codec<X, Y, Z> $c
     * @return self<X, Z>
     */
    public static function fromCodec(Codec $c): self
    {
        return new self(
        /**
         * @param X $x
         * @psalm-return Z
         * @psalm-suppress MixedInferredReturnType
         */
            function ($x) use ($c) {
                /**
                 * @psalm-suppress MixedReturnStatement
                 */
                return $c->encode($x);
            }
        );
    }

    /**
     * @param callable(A):O $encode
     */
    public function __construct(callable $encode)
    {
        $this->encode = $encode;
    }

    /**
     * @param A $a
     * @return O
     */
    public function __invoke($a)
    {
        return ($this->encode)($a);
    }
}
