<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Internal\Primitives\UndefinedDecoder;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress UndefinedFunction
 */
class UndefinedDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testDefault(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $this
            ->forAll(GeneratorUtils::scalar())
            ->then(
                /** @psalm-param scalar $default */
                function ($default): void {
                    $x = self::assertValidationSuccess(
                        Decoders::undefined($default)->decode(new Undefined())
                    );

                    self::assertSame($default, $x);
                }
            );
    }

    // Aggiunti Michele
    public function testValidUndefined(): void
    {
        $default = 'default-value';
        $decoder = new UndefinedDecoder($default);
        $result = $decoder->decode(new Undefined());

        // fwrite(STDOUT, "\n[testValidUndefined] result: " . var_export($result, true) . "\n");

        $this->assertInstanceOf(ValidationSuccess::class, $result);

        $this->assertSame($default, $result->getValue());
    }

    /**
     * @dataProvider provideInvalidValues
     *
     * @param mixed $input
     */
    public function testInvalidValues($input): void
    {
        $decoder = new UndefinedDecoder('default');
        $result = $decoder->decode($input);

        // fwrite(STDOUT, "\n[testInvalidValues] input: " . var_export($input, true) . "\n");
        // fwrite(STDOUT, "[testInvalidValues] result: " . var_export($result, true) . "\n");

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function provideInvalidValues(): array
    {
        return [
            'null' => [null],
            'string' => ['undefined'],
            'int' => [0],
            'bool' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }
}
