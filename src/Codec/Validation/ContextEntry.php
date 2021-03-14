<?php declare(strict_types=1);

namespace Pybatt\Codec\Validation;

use Pybatt\Codec\Decoder;

class ContextEntry
{
    /** @var string */
    private $key;
    /** @var Decoder */
    private $decoder;
    /** @var mixed */
    private $actual;

    /**
     * @param string $key
     * @param Decoder<mixed, mixed> $decoder
     * @param mixed $actual
     */
    public function __construct(
        string $key,
        Decoder $decoder,
        $actual
    )
    {
        $this->key = $key;
        $this->decoder = $decoder;
        $this->actual = $actual;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDecoder(): Decoder
    {
        return $this->decoder;
    }

    /**
     * @return mixed
     */
    public function getActual()
    {
        return $this->actual;
    }
}
