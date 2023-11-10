<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\DecoderForSumType;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class DecoderForSumType extends BaseTestCase
{
    use TestTrait;

    public function testSumTypes(): void
    {
        $decoder = Decoders::union(
            Decoders::classFromArrayPropsDecoder(
                Decoders::arrayProps([
                    'type' => Decoders::literal(P::Type_a),
                    'subType' => Decoders::union(
                        Decoders::literal(A::SUB_foo),
                        Decoders::literal(A::SUB_bar)
                    ),
                    'propA' => Decoders::int(),
                    'propB' => Decoders::string(),
                ]),
                fn(string $t, string $subT, int $propA, string $propB): A => new A($subT, $propA, $propB),
                A::class
            ),
            Decoders::classFromArrayPropsDecoder(
                Decoders::arrayProps([
                    'type' => Decoders::literal(P::Type_b),
                    'case' => Decoders::union(
                        Decoders::literal(B::CASE_B1),
                        Decoders::literal(B::CASE_B2),
                        Decoders::literal(B::CASE_B3)
                    ),
                    'amount' => Decoders::float(),
                    'flag' => Decoders::bool(),
                ]),
                fn(string $t, int $case, float $amount, bool $flag): B => new B($case, $amount, $flag),
                B::class
            )
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::associative([
                    'type' => Generators::constant(P::Type_a),
                    'subType' => Generators::elements(A::SUB_foo, A::SUB_bar),
                    'propA' => Generators::int(),
                    'propB' => Generators::string(),
                ])
            )
            ->then(function (array $i) use ($decoder): void {
                $result = $decoder->decode($i);

                $a = self::assertSuccessInstanceOf(A::class, $result);

                self::assertSame($i['subType'], $a->getSubType());
                self::assertSame($i['propA'], $a->getPropertyA());
                self::assertSame($i['propB'], $a->getPropertyB());
            });

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::associative([
                    'type' => Generators::constant(P::Type_b),
                    'case' => Generators::elements(B::CASE_B1, B::CASE_B2, B::CASE_B3),
                    'amount' => Generators::float(),
                    'flag' => Generators::bool(),
                ])
            )
            ->then(function (array $i) use ($decoder): void {
                $result = $decoder->decode($i);

                $b = self::assertSuccessInstanceOf(B::class, $result);

                self::assertSame($i['case'], $b->getCase());
                self::assertEquals($i['amount'], $b->getAmount());
                self::assertSame($i['flag'], $b->isFlag());
            });
    }
}
