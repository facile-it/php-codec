<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class OpenWeatherResponse
{
    public function __construct(private readonly \Examples\Facile\PhpCodec\DecodeApiResponse\Coordinates $coordinates, private readonly array $weather, private readonly \Examples\Facile\PhpCodec\DecodeApiResponse\Sys $sys)
    {
    }

    public function getCoordinates(): \Examples\Facile\PhpCodec\DecodeApiResponse\Coordinates
    {
        return $this->coordinates;
    }

    public function getWeather(): array
    {
        return $this->weather;
    }

    public function getSys(): Sys
    {
        return $this->sys;
    }
}
