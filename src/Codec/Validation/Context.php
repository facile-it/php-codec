<?php declare(strict_types=1);

namespace Pybatt\Codec\Validation;

class Context implements \Iterator
{
    /** @var list<ContextEntry> */
    private $entries;
    /** @var int */
    private $currentIndex;

    public static function mempty(): self
    {
        return new self();
    }

    public function __construct(
        ContextEntry ...$entries
    )
    {
        $this->entries = $entries;
        $this->currentIndex = 0;
    }

    public function appendEntries(ContextEntry ...$entries): self
    {
        return new self(
            ...array_merge(
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
        $this->currentIndex++;
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
