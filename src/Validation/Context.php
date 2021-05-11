<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

use Facile\PhpCodec\Decoder;

final class Context implements \Iterator
{
    /** @var ContextEntry[] */
    private $entries;
    /** @var int */
    private $currentIndex;
    /** @var Decoder */
    private $decoder;

    /**
     * @psalm-param Decoder $decoder
     * @psalm-param ContextEntry ...$entries
     */
    public function __construct(
        Decoder $decoder,
        ContextEntry ...$entries
    ) {
        $this->entries = $entries;
        $this->currentIndex = 0;
        $this->decoder = $decoder;
    }

    public function appendEntries(ContextEntry ...$entries): self
    {
        return new self(
            $this->decoder,
            ...\array_merge(
                $this->entries,
                $entries
            )
        );
    }

    public function current(): ContextEntry
    {
        return $this->entries[$this->currentIndex];
    }

    public function next(): void
    {
        ++$this->currentIndex;
    }

    public function key(): int
    {
        return $this->currentIndex;
    }

    public function valid(): bool
    {
        return isset($this->entries[$this->currentIndex]);
    }

    public function rewind(): void
    {
        $this->currentIndex = 0;
    }
}
