<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

use Facile\PhpCodec\Decoder;

final class ContextEntry
{
    /**
     * @psalm-param string $key
     * @psalm-param Decoder<mixed, mixed> $decoder
     * @psalm-param mixed  $actual
     *
     * @param mixed $actual
     */
    public function __construct(private readonly string $key, private readonly \Facile\PhpCodec\Decoder $decoder, private $actual)
    {
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
