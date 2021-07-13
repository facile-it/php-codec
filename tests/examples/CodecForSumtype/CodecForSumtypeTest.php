<?php

declare(strict_types=1);

namespace Examples\Facile\PhpCodec\CodecForSumtype;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class CodecForSumtypeTest extends BaseTestCase
{
    use TestTrait;

    public function testSumTypes(): void
    {
        $codec = Decoders::union(
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
                function (string $t, string $subT, int $propA, string $propB): A {
                    return new A($subT, $propA, $propB);
                },
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
                function (string $t, int $case, float $amount, bool $flag): B {
                    return new B($case, $amount, $flag);
                },
                B::class
            )
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\associative([
                    'type' => g\constant(P::Type_a),
                    'subType' => g\elements(A::SUB_foo, A::SUB_bar),
                    'propA' => g\int(),
                    'propB' => g\string(),
                ])
            )
            ->then(function (array $i) use ($codec): void {
                $result = $codec->decode($i);

                $a = self::assertSuccessInstanceOf(A::class, $result);

                self::assertSame($i['subType'], $a->getSubType());
                self::assertSame($i['propA'], $a->getPropertyA());
                self::assertSame($i['propB'], $a->getPropertyB());
            });

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\associative([
                    'type' => g\constant(P::Type_b),
                    'case' => g\elements(B::CASE_B1, B::CASE_B2, B::CASE_B3),
                    'amount' => g\float(),
                    'flag' => g\bool(),
                ])
            )
            ->then(function (array $i) use ($codec): void {
                $result = $codec->decode($i);

                $b = self::assertSuccessInstanceOf(B::class, $result);

                self::assertSame($i['case'], $b->getCase());
                self::assertEquals($i['amount'], $b->getAmount());
                self::assertSame($i['flag'], $b->isFlag());
            });
    }
}
