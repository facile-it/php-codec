<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Internal\Useful\DateTimeFromStringDecoder;
use Tests\Facile\PhpCodec\BaseTestCase;

class DateTimeFromStringDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function test(): void
    {
        $decoder = new DateTimeFromStringDecoder();
        self::asserSuccessInstanceOf(
            \DateTimeInterface::class,
            $decoder->decode('2021-03-12T06:22:48+01:00')
        );

        $this
            ->forAll(
                g\date()
            )
            ->then(function (\DateTimeInterface $date) use ($decoder): void {
                self::asserSuccessInstanceOf(
                    \DateTimeInterface::class,
                    $decoder->decode($date->format(\DATE_ATOM))
                );
            });

        $this
            ->limitTo(1000)
            ->forAll(
                g\date(),
                g\elements([
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
                $decoder = new DateTimeFromStringDecoder($format);

                self::asserSuccessInstanceOf(
                    \DateTimeInterface::class,
                    $decoder->decode($date->format($format))
                );
            });
    }
}
