<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
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
            ->limitTo(1000)
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
}
