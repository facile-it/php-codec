<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

use Facile\PhpCodec\Decoder;

final class ContextEntry
{
    /** @var string */
    private $key;
    /** @var Decoder */
    private $decoder;
    /** @var mixed */
    private $actual;

    /**
     * @psalm-param string $key
     * @psalm-param Decoder<mixed, mixed> $decoder
     * @psalm-param mixed  $actual
     */
    public function __construct(
        string $key,
        Decoder $decoder,
        $actual
    ) {
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
