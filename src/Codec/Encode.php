<?php declare(strict_types=1);

namespace Pybatt\Codec;

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
        return new self(
        /**
         * @param X $a
         * @return X
         */
            function ($a) {
                return $a;
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
