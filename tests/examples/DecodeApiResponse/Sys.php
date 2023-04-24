<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class Sys
{
    private string $country;
    private \DateTimeInterface $sunrise;
    private \DateTimeInterface $sunset;

    public function __construct(string $country, \DateTimeInterface $sunrise, \DateTimeInterface $sunset)
    {
        $this->country = $country;
        $this->sunrise = $sunrise;
        $this->sunset = $sunset;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getSunrise(): \DateTimeInterface
    {
        return $this->sunrise;
    }

    public function getSunset(): \DateTimeInterface
    {
        return $this->sunset;
    }
}
