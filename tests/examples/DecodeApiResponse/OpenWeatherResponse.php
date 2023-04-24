<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class OpenWeatherResponse
{
    private \Examples\Facile\PhpCodec\DecodeApiResponse\Coordinates $coordinates;
    private array $weather;
    private \Examples\Facile\PhpCodec\DecodeApiResponse\Sys $sys;

    public function __construct(
        \Examples\Facile\PhpCodec\DecodeApiResponse\Coordinates $coordinates,
        array $weathers,
        Sys $sys
    ) {
        $this->coordinates = $coordinates;
        $this->weather = $weathers;
        $this->sys = $sys;
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
