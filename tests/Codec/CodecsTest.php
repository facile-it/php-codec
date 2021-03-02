<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec;

use Eris\Generator;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\CommonTypes\ClassFromArray;
use Pybatt\Codec\CommonTypes\UnionType;
use Pybatt\Codec\ValidationFailures;
use Pybatt\Codec\ValidationSuccess;

class CodecsTest extends TestCase
{
    use TestTrait;

    public function testCodec(): void
    {
        $nullCodec = Codecs::null();

        self::assertInstanceOf(
            ValidationSuccess::class,
            $nullCodec->decode(null)
        );

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\int(),
                    Generator\float(),
                    Generator\date(),
                    Generator\string(),
                    Generator\bool()
                )
            )
            ->then(function ($x) use ($nullCodec) {
                self::assertInstanceOf(
                    ValidationFailures::class,
                    $nullCodec->decode($x)
                );
            });

        $this
            ->forAll(Generator\string())
            ->then(function ($x) {
                /** @var ValidationSuccess $validation */
                $validation = Codecs::string()->decode($x);
                self::assertInstanceOf(ValidationSuccess::class, $validation);
                self::assertSame($x, $validation->getValue());
            });

        $this
            ->forAll(Generator\int())
            ->then(function ($x) {
                /** @var ValidationSuccess $validation */
                $validation = Codecs::int()->decode($x);
                self::assertInstanceOf(ValidationSuccess::class, $validation);
                self::assertSame($x, $validation->getValue());
            });
    }

    public function testDecodeMapToClass(): void {
        $type = new ClassFromArray(
            [
                'foo' => Codecs::string(),
                'bar' => Codecs::int()
            ],
            function (string $foo, int $bar) {
                return new A($foo, $bar);
            },
            A::class
        );

        $this
            ->forAll(
                Generator\associative([
                    'foo' => Generator\string(),
                    'bar' => Generator\int()
                ])
            )
            ->then(function (array $i) use ($type) {
                /** @var ValidationSuccess<A> $validation */
                $validation = $type->decode($i);

                self::assertInstanceOf(ValidationSuccess::class, $validation);
                self::assertInstanceOf(A::class, $validation->getValue());
                self::assertSame($i['foo'], $validation->getValue()->getFoo());
                self::assertSame($i['bar'], $validation->getValue()->getBar());
            });


        $this
            ->forAll(
                Generator\associative([
                    'foo' => Generator\oneOf(
                        Generator\int(),
                        Generator\float(),
                        Generator\date(),
                        Generator\bool()
                    ),
                    'bar' => Generator\oneOf(
                        Generator\float(),
                        Generator\date(),
                        Generator\bool(),
                        Generator\string()
                    )
                ])
            )
            ->then(function (array $i) use ($type) {
                $validation = $type->decode($i);

                self::assertInstanceOf(ValidationFailures::class, $validation);
            });
    }

    public function testUnionType(): void {
        $type = new UnionType(
            new ClassFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::int()
                ],
                function (string $foo, int $bar) {
                    return new A($foo, $bar);
                },
                A::class
            ),
            Codecs::null()
        );

        $this
            ->forAll(
                Generator\associative([
                    'foo' => Generator\string(),
                    'bar' => Generator\int()
                ])
            )
            ->then(function (array $i) use ($type) {
                /** @var ValidationSuccess<A> $validation */
                $validation = $type->decode($i);

                self::assertInstanceOf(ValidationSuccess::class, $validation);
                self::assertInstanceOf(A::class, $validation->getValue());
                self::assertSame($i['foo'], $validation->getValue()->getFoo());
                self::assertSame($i['bar'], $validation->getValue()->getBar());
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\associative([
                        'foo' => Generator\string(),
                        'bar' => Generator\int()
                    ]),
                    Generator\constant(null)
                )
            )
            ->then(function ($i) use ($type) {
                $validation = $type->decode($i);
                self::assertInstanceOf(ValidationSuccess::class, $validation);
            });
    }
}

class A {
    /** @var string */
    private $foo;
    /** @var int */
    private $bar;

    public function __construct(
        string $foo,
        int $bar
    )
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): int
    {
        return $this->bar;
    }
}
