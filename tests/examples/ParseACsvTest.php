<?php declare(strict_types=1);

namespace Examples\Pybatt\Codec;

use Examples\Pybatt\Codec\ParseACsvTest\in;
use Pybatt\Codec\Codecs;
use Tests\Pybatt\Codec\BaseTestCase;

class ParseACsvTest extends BaseTestCase
{
    public function test(): void
    {
        $simpleCsv = <<<CSV
1,Milano,F205
2,Roma,H501
3,Monte Urano,F653
CSV;

        $codec = Codecs::listt(
            Codecs::pipe(
                Codecs::string(),
                Codecs::regex('/^(?<id>\d),(?<name>.*),(?<code>[A-Z]{1}\d{3})$/'),
                Codecs::classFromArray(
                    [
                        'id' => Codecs::intFromString(),
                        'name' => Codecs::string(),
                        'code' => Codecs::string()
                    ],
                    function (int $id, string $name, string $code): in\City {
                        return new in\City($id, $name, $code);
                    },
                    in\City::class
                )
            )
        );

        $result = $codec->decode(
            explode("\n", $simpleCsv)
        );

        self::asserSuccessAnd(
            $result,
            function (array $cs) {
                /** @var in\City[] $cs */
                self::assertContainsOnlyInstancesOf(in\City::class, $cs);

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
        );
    }
}

namespace Examples\Pybatt\Codec\ParseACsvTest\in;

class City
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $italianLandRegistryCode;

    public function __construct(
        int $id,
        string $name,
        string $italianLandRegistryCode
    )
    {
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
