<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class DateTimeFromStringDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function test(): void
    {
        $decoder = Decoders::dateTimeFromString();
        self::assertSuccessInstanceOf(
            \DateTimeInterface::class,
            $decoder->decode('2021-03-12T06:22:48+01:00')
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::date()
            )
            ->then(function (\DateTimeInterface $date) use ($decoder): void {
                self::assertSuccessInstanceOf(
                    \DateTimeInterface::class,
                    $decoder->decode($date->format(\DATE_ATOM))
                );
            });

        /** @psalm-suppress UndefinedFunction */
        $this
            ->limitTo(1_000)
            ->forAll(
                Generators::date(),
                Generators::elements([
                    \DATE_ATOM,
                    \DATE_COOKIE,
                    \DATE_ISO8601,
                    \DATE_RFC822,
                    \DATE_RFC850,
                    \DATE_RFC1036,
                    \DATE_RFC1123,
                    \DATE_RFC2822,
                    \DATE_RFC3339,
                    \DATE_RFC7231,
                    \DATE_RSS,
                    \DATE_W3C,
                ])
            )
            ->then(function (\DateTimeInterface $date, string $format): void {
                /** @psalm-suppress InternalClass */
                $decoder = Decoders::dateTimeFromString($format);

                self::assertSuccessInstanceOf(
                    \DateTimeInterface::class,
                    $decoder->decode($date->format($format))
                );
            });
    }

    public function testStrictMode(): void
    {
        // Test strict mode (default) - should reject invalid dates
        $strictDecoder = Decoders::dateTimeFromString('Y-m-d', true);
        
        // Valid date should pass
        self::assertSuccessInstanceOf(
            \DateTimeInterface::class,
            $strictDecoder->decode('2025-04-30')
        );
        
        // Invalid date should fail in strict mode (April 31st doesn't exist)
        self::assertInstanceOf(
            ValidationFailures::class,
            $strictDecoder->decode('2025-04-31')
        );
        
        // Invalid leap year date should fail in strict mode
        self::assertInstanceOf(
            ValidationFailures::class,
            $strictDecoder->decode('2023-02-29')
        );
        
        // Valid leap year date should pass in strict mode
        self::assertSuccessInstanceOf(
            \DateTimeInterface::class,
            $strictDecoder->decode('2024-02-29')
        );
        
        // Test non-strict mode - should accept invalid dates and adjust them
        $nonStrictDecoder = Decoders::dateTimeFromString('Y-m-d', false);
        
        // Valid date should pass
        self::assertSuccessInstanceOf(
            \DateTimeInterface::class,
            $nonStrictDecoder->decode('2025-04-30')
        );
        
        // Invalid date should pass in non-strict mode (but be adjusted)
        $result = $nonStrictDecoder->decode('2025-04-31');
        $successResult = self::assertSuccessInstanceOf(\DateTimeInterface::class, $result);
        self::assertEquals('2025-05-01', $successResult->format('Y-m-d'));
    }
}
