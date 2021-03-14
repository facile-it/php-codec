<?php declare(strict_types=1);

namespace Examples\Pybatt\Codec;

use Eris\Generator as g;
use Eris\TestTrait;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\Internal\Primitives\LitteralType;
use Pybatt\Codec\Validation\ValidationSuccess;
use Tests\Pybatt\Codec\BaseTestCase;

class SumTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testSumTypes(): void
    {
        $codec = Codecs::union(
            Codecs::classFromArray(
                [
                    'type' => new LitteralType(internal\P::Type_a),
                    'subType' => Codecs::union(
                        new LitteralType(internal\A::SUB_foo),
                        new LitteralType(internal\A::SUB_bar)
                    ),
                    'propA' => Codecs::int(),
                    'propB' => Codecs::string()
                ],
                function (string $t, string $subT, int $propA, string $propB): internal\A {
                    return new internal\A($subT, $propA, $propB);
                },
                internal\A::class
            ),
            Codecs::classFromArray(
                [
                    'type' => new LitteralType(internal\P::Type_b),
                    'case' => Codecs::union(
                        new LitteralType(internal\B::CASE_B1),
                        new LitteralType(internal\B::CASE_B2),
                        new LitteralType(internal\B::CASE_B3)
                    ),
                    'amount' => Codecs::float(),
                    'flag' => Codecs::bool()
                ],
                function (string $t, int $case, float $amount, bool $flag): internal\B {
                    return new internal\B($case, $amount, $flag);
                },
                internal\B::class
            )
        );

        $this
            ->forAll(
                g\associative([
                    'type' => g\constant(internal\P::Type_a),
                    'subType' => g\elements(internal\A::SUB_foo, internal\A::SUB_bar),
                    'propA' => g\int(),
                    'propB' => g\string()
                ])
            )
            ->then(function ($i) use ($codec) {
                /** @var ValidationSuccess $result */
                $result = $codec->decode($i);

                self::asserSuccessInstanceOf(
                    internal\A::class,
                    $result,
                    function (internal\A $a) use ($i) {
                        self::assertSame($i['subType'], $a->getSubType());
                        self::assertSame($i['propA'], $a->getPropertyA());
                        self::assertSame($i['propB'], $a->getPropertyB());
                    }
                );
            });

        $this
            ->forAll(
                g\associative([
                    'type' => g\constant(internal\P::Type_b),
                    'case' => g\elements(internal\B::CASE_B1, internal\B::CASE_B2, internal\B::CASE_B3),
                    'amount' => g\float(),
                    'flag' => g\bool()
                ])
            )
            ->then(function ($i) use ($codec) {
                /** @var ValidationSuccess $result */
                $result = $codec->decode($i);

                self::asserSuccessInstanceOf(
                    internal\B::class,
                    $result,
                    function(internal\B $b) use ($i) {
                        self::assertSame($i['case'], $b->getCase());
                        self::assertEquals($i['amount'], $b->getAmount());
                        self::assertSame($i['flag'], $b->isFlag());
                    }
                );
            });
    }
}

namespace Examples\Pybatt\Codec\internal;

abstract class P
{
    public const Type_a = 'a';
    public const Type_b = 'b';

    abstract public function getType(): string;
}

class A extends P
{

    public const SUB_foo = 'foo';
    public const SUB_bar = 'bar';

    /** @var string */
    private $subType;
    /** @var int */
    private $propertyA;
    /** @var string */
    private $propertyB;

    public function __construct(string $subType, int $propertyA, string $propertyB)
    {
        $this->subType = $subType;
        $this->propertyA = $propertyA;
        $this->propertyB = $propertyB;
    }

    public function getType(): string
    {
        return self::Type_a;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function getPropertyA(): int
    {
        return $this->propertyA;
    }

    public function getPropertyB(): string
    {
        return $this->propertyB;
    }
}

class B extends P
{
    public const CASE_B1 = 1;
    public const CASE_B2 = 2;
    public const CASE_B3 = 3;

    /** @var int */
    private $case;
    /** @var float */
    private $amount;
    /** @var bool */
    private $flag;

    public function __construct(int $case, float $amount, bool $flag)
    {
        $this->case = $case;
        $this->amount = $amount;
        $this->flag = $flag;
    }

    public function getType(): string
    {
        return self::Type_b;
    }

    public function getCase(): int
    {
        return $this->case;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function isFlag(): bool
    {
        return $this->flag;
    }
}
