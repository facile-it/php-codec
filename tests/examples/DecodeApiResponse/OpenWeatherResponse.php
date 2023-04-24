<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class OpenWeatherResponse
{
    /** @var \Examples\Facile\PhpCodec\DecodeApiResponse\Coordinates */
    private $coordinates;
    /** @var array */
    private $weather;
    /** @var Sys */
    private $sys;

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
