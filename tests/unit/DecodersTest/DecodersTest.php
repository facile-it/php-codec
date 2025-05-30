<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\DecodersTest;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\ValidationSuccess;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\TypeAssertions;

class DecodersTest extends BaseTestCase
{
    use TestTrait;

    public function testMap(): void
    {
        $decoder = Decoders::transformValidationSuccess(
            fn(int $v): A => new A($v),
            Decoders::int()
        );

        $this
            ->forAll(
                Generators::int()
            )
            ->then(function (int $i) use ($decoder): void {
                $a = self::assertSuccessInstanceOf(
                    A::class,
                    $decoder->decode($i)
                );
                self::assertSame($i, $a->getValue());
            });
    }

    public function testPipe(): void
    {
        $d = Decoders::pipe(
            Decoders::string(),
            Decoders::stringMatchingRegex('/^\d+$/'),
        );
        TypeAssertions::decoderMixedString($d);
        $validation = $d->decode('3');
        self::assertInstanceOf(ValidationSuccess::class, $validation);
        self::assertSame('3', $validation->getValue());

        $d = Decoders::pipe(
            Decoders::string(),
            Decoders::stringMatchingRegex('/^\d+$/'),
            Decoders::intFromString(),
        );
        TypeAssertions::decoderMixedInt($d);
        $validation = $d->decode('3');
        self::assertInstanceOf(ValidationSuccess::class, $validation);
        self::assertSame(3, $validation->getValue());

        $d = Decoders::pipe(
            Decoders::string(),
            Decoders::stringMatchingRegex('/^\d+$/'),
            Decoders::intFromString(),
            Decoders::make(fn(int $i) => new ValidationSuccess((float) $i)),
        );
        TypeAssertions::decoderMixedFloat($d);
        $validation = $d->decode('3');
        self::assertInstanceOf(ValidationSuccess::class, $validation);
        self::assertSame(3.0, $validation->getValue());

        $d = Decoders::pipe(
            Decoders::string(),
            Decoders::stringMatchingRegex('/^\d+$/'),
            Decoders::intFromString(),
            Decoders::make(fn(int $i) => new ValidationSuccess((float) $i)),
            Decoders::make(fn(float $i) => Validation::success($i > 0))
        );
        TypeAssertions::decoderMixedBool($d);
        $validation = $d->decode('3');
        self::assertInstanceOf(ValidationSuccess::class, $validation);
        self::assertTrue($validation->getValue());
    }
}
