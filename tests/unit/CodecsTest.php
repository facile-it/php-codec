<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;

/** @psalm-suppress PropertyNotSetInConstructor */
class CodecsTest extends BaseTestCase
{
    use TestTrait;

    public function testCodec(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $nullCodec = Codecs::null();

        self::assertInstanceOf(
            ValidationSuccess::class,
            $nullCodec->decode(null)
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\oneOf(
                    g\int(),
                    g\float(),
                    g\date(),
                    g\string(),
                    g\bool()
                )
            )
            ->then(
                /** @psalm-param mixed $x */
                function ($x) use ($nullCodec): void {
                    self::assertInstanceOf(
                        ValidationFailures::class,
                        $nullCodec->decode($x)
                    );
                }
            );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(g\string())
            ->then(
                /** @psalm-param mixed $x */
                function ($x): void {
                    /** @psalm-suppress DeprecatedMethod */
                    self::assertSame(
                        $x,
                        self::assertValidationSuccess(Codecs::string()->decode($x))
                    );
                }
            );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(g\int())
            ->then(
                function (int $x): void {
                    /** @psalm-suppress DeprecatedMethod */
                    self::assertSame(
                        $x,
                        self::assertValidationSuccess(Codecs::int()->decode($x))
                    );
                }
            );
    }

    public function testDecodeMapToClass(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $type = Codecs::classFromArray(
            [
                'foo' => Codecs::string(),
                'bar' => Codecs::int(),
            ],
            function (string $foo, int $bar): in\A {
                return new in\A($foo, $bar);
            },
            in\A::class
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\associative([
                    'foo' => g\string(),
                    'bar' => g\int(),
                ])
            )
            ->then(function (array $i) use ($type): void {
                $r = self::assertSuccessInstanceOf(in\A::class, $type->decode($i));
                self::assertSame($i['foo'], $r->getFoo());
                self::assertSame($i['bar'], $r->getBar());
            });

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\associative([
                    'foo' => g\oneOf(
                        g\int(),
                        g\float(),
                        g\date(),
                        g\bool()
                    ),
                    'bar' => g\oneOf(
                        g\float(),
                        g\date(),
                        g\bool(),
                        g\string()
                    ),
                ])
            )
            ->then(function (array $i) use ($type): void {
                $validation = $type->decode($i);

                self::assertInstanceOf(ValidationFailures::class, $validation);
            });
    }

    public function testUnionType(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $type = Codecs::union(
            Codecs::classFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::int(),
                ],
                function (string $foo, int $bar): in\A {
                    return new in\A($foo, $bar);
                },
                in\A::class
            ),
            Codecs::null()
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\associative([
                    'foo' => g\string(),
                    'bar' => g\int(),
                ])
            )
            ->then(function (array $i) use ($type): void {
                $a = self::assertSuccessInstanceOf(
                    in\A::class,
                    $type->decode($i)
                );

                self::assertSame($i['foo'], $a->getFoo());
                self::assertSame($i['bar'], $a->getBar());
            });

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\oneOf(
                    g\associative([
                        'foo' => g\string(),
                        'bar' => g\int(),
                    ]),
                    g\constant(null)
                )
            )
            ->then(function (?array $i) use ($type): void {
                self::assertValidationSuccess($type->decode($i));
            });
    }

    public function testListType(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $type = Codecs::listt(
            Codecs::classFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::int(),
                ],
                function (string $f, int $b): in\A {
                    return new in\A($f, $b);
                },
                in\A::class
            )
        );

        /**
         * @psalm-suppress UndefinedFunction
         * @psalm-suppress MixedInferredReturnType
         */
        $this
            ->forAll(
                g\bind(
                    g\choose(3, 10),
                    function (int $size): g {
                        /**
                         * @psalm-suppress UndefinedFunction
                         * @psalm-suppress MixedReturnStatement
                         */
                        return g\vector(
                            $size,
                            g\associative([
                                'foo' => g\string(),
                                'bar' => g\int(),
                            ])
                        );
                    }
                )
            )
            ->then(function (array $list) use ($type): void {
                $decoded = self::assertValidationSuccess($type->decode($list));

                self::assertCount(\count($list), $decoded);
                self::assertContainsOnly(in\A::class, $decoded);
            });
    }

    public function testComposition(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $type = Codecs::pipe(
            Codecs::string(),
            Codecs::regex('/^foo:(?<foo>\w{2,5})#bar:(?<bar>\d{1,3})$/'),
            Codecs::classFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::pipe(Codecs::string(), Codecs::intFromString()),
                ],
                [in\A::class, 'create'],
                in\A::class
            )
        );

        self::assertSuccessInstanceOf(
            in\A::class,
            $type->decode('foo:abc#bar:123')
        );
    }

    public function testIntCodecLaws(): void
    {
        /**
         * @psalm-suppress UndefinedFunction
         * @psalm-suppress DeprecatedMethod
         */
        $this
            ->forAll(
                GeneratorUtils::scalar(),
                g\int()
            )
            ->then(self::codecLaws(Codecs::int()));
    }
}

namespace Tests\Facile\PhpCodec\in;

class A
{
    /** @var string */
    private $foo;
    /** @var int */
    private $bar;

    public static function create(string $foo, int $bar): self
    {
        return new self($foo, $bar);
    }

    public function __construct(
        string $foo,
        int $bar
    ) {
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
