<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecodeApiResponse;

use Facile\PhpCodec\Decoders;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class DecodeApiResponseTest extends BaseTestCase
{
    public function testJsonDecoding(): void
    {
        $decoder = Decoders::classFromArrayPropsDecoder(
            Decoders::arrayProps([
                'coord' => Decoders::classFromArrayPropsDecoder(
                    Decoders::arrayProps([
                        'lon' => Decoders::float(),
                        'lat' => Decoders::float(),
                    ]),
                    fn(float $lon, float $lat): Coordinates => new Coordinates($lon, $lat),
                    Coordinates::class
                ),
                'weather' => Decoders::listOf(
                    Decoders::classFromArrayPropsDecoder(
                        Decoders::arrayProps([
                            'id' => Decoders::int(),
                            'main' => Decoders::string(),
                            'description' => Decoders::string(),
                        ]),
                        fn(int $id, string $main, string $desc): Weather => new Weather($id, $main, $desc),
                        Weather::class
                    )
                ),
                'sys' => Decoders::classFromArrayPropsDecoder(
                    Decoders::arrayProps([
                        'country' => Decoders::string(),
                        'sunrise' => Decoders::dateTimeFromString(),
                        'sunset' => Decoders::dateTimeFromString(),
                    ]),
                    fn(string $county, \DateTimeInterface $sunrise, \DateTimeInterface $sunset): Sys => new Sys($county, $sunrise, $sunset),
                    Sys::class
                ),
            ]),
            fn(Coordinates $coordinates, array $weathers, Sys $sys): OpenWeatherResponse => new OpenWeatherResponse($coordinates, $weathers, $sys),
            OpenWeatherResponse::class
        );

        $result = $decoder->decode(\json_decode(self::weatherJson(), true, 512, JSON_THROW_ON_ERROR));

        self::assertSuccessInstanceOf(OpenWeatherResponse::class, $result);
    }

    private static function weatherJson(): string
    {
        return <<<'JSON'
            {
              "coord": {
                "lon": 13.6729,
                "lat": 43.2027
              },
              "weather": [
                {
                  "id": 804,
                  "main": "Clouds",
                  "description": "overcast clouds",
                  "icon": "04d"
                }
              ],
              "base": "stations",
              "main": {
                "temp": 286.82,
                "feels_like": 286.01,
                "temp_min": 285.93,
                "temp_max": 288.15,
                "pressure": 1015,
                "humidity": 74
              },
              "visibility": 10000,
              "wind": {
                "speed": 0.89,
                "deg": 270,
                "gust": 0.89
              },
              "clouds": {
                "all": 100
              },
              "dt": 1615564151,
              "sys": {
                "type": 3,
                "id": 2001891,
                "country": "IT",
                "sunrise": "2021-03-12T06:22:48+01:00",
                "sunset": "2021-03-12T18:07:28+01:00"
              },
              "timezone": 3600,
              "id": 3172720,
              "name": "Monte Urano",
              "cod": 200
            }
            JSON;
    }
}
