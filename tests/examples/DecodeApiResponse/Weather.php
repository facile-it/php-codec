<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class Weather
{
    private int $id;
    private string $main;
    private string $description;

    public function __construct(int $id, string $main, string $description)
    {
        $this->id = $id;
        $this->main = $main;
        $this->description = $description;
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
