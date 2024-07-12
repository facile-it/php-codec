<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class Sys
{
    public function __construct(private readonly string $country, private readonly \DateTimeInterface $sunrise, private readonly \DateTimeInterface $sunset)
    {
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
