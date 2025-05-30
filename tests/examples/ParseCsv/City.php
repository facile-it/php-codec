<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\ParseCsv;

class City
{
    public function __construct(private readonly int $id, private readonly string $name, private readonly string $italianLandRegistryCode) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItalianLandRegistryCode(): string
    {
        return $this->italianLandRegistryCode;
    }
}
