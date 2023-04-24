<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

/**
 * @psalm-internal Examples\Facile\PhpCodec\DecodeApiResponse
 */
class Sys
{
    /** @var string */
    private $country;
    /** @var \DateTimeInterface */
    private $sunrise;
    /** @var \DateTimeInterface */
    private $sunset;

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
