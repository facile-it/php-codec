<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\ParseCsv;

class City
{
    private int $id;
    private string $name;
    private string $italianLandRegistryCode;

    public function __construct(
        int $id,
        string $name,
        string $italianLandRegistryCode
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->italianLandRegistryCode = $italianLandRegistryCode;
    }

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
