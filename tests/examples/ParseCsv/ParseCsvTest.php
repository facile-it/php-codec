<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\ParseCsv;

use Facile\PhpCodec\Decoders;
use Tests\Facile\PhpCodec\BaseTestCase;

class ParseCsvTest extends BaseTestCase
{
    public function test(): void
    {
        $simpleCsv = <<<'CSV'
            1,Milano,F205
            2,Roma,H501
            3,Monte Urano,F653
            CSV;
        $decoder = Decoders::listOf(
            Decoders::pipe(
                Decoders::string(),
                Decoders::regex('/^(?<id>\d),(?<name>.*),(?<code>[A-Z]{1}\d{3})$/'),
                Decoders::classFromArrayPropsDecoder(
                    Decoders::arrayProps([
                        'id' => Decoders::intFromString(),
                        'name' => Decoders::string(),
                        'code' => Decoders::string(),
                    ]),
                    static fn(int $id, string $name, string $code): City => new City($id, $name, $code),
                    City::class
                )
            )
        );

        $result = $decoder->decode(
            \explode("\n", $simpleCsv)
        );

        $cs = self::assertValidationSuccess($result);

        self::assertContainsOnlyInstancesOf(City::class, $cs);

        [$milano, $roma, $mu] = $cs;

        self::assertSame(1, $milano->getId());
        self::assertSame('Milano', $milano->getName());
        self::assertSame('F205', $milano->getItalianLandRegistryCode());

        self::assertSame(2, $roma->getId());
        self::assertSame('Roma', $roma->getName());
        self::assertSame('H501', $roma->getItalianLandRegistryCode());

        self::assertSame(3, $mu->getId());
        self::assertSame('Monte Urano', $mu->getName());
        self::assertSame('F653', $mu->getItalianLandRegistryCode());
    }
}
