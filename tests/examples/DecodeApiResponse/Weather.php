<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class Weather
{
    public function __construct(private readonly int $id, private readonly string $main, private readonly string $description)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMain(): string
    {
        return $this->main;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
