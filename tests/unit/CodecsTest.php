<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec;

use Eris\Generator as g;
use Eris\TestTrait;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\Internal\Combinators\ClassFromArray;
use Pybatt\Codec\Internal\Useful\RegexType;
use Pybatt\Codec\Validation\ValidationFailures;
use Pybatt\Codec\Validation\ValidationSuccess;

class CodecsTest extends BaseTestCase
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
                g\oneOf(
                    g\int(),
                    g\float(),
                    g\date(),
                    g\string(),
                    g\bool()
                )
            )
            ->then(function ($x) use ($nullCodec) {
                self::assertInstanceOf(
                    ValidationFailures::class,
                    $nullCodec->decode($x)
                );
            });

        $this
            ->forAll(g\string())
            ->then(function ($x) {
                /** @var ValidationSuccess $validation */
                $validation = Codecs::string()->decode($x);
                self::assertInstanceOf(ValidationSuccess::class, $validation);
                self::assertSame($x, $validation->getValue());
            });

        $this
            ->forAll(g\int())
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
            function (string $foo, int $bar): in\A {
                return new in\A($foo, $bar);
            },
            in\A::class
        );

        $this
            ->forAll(
                g\associative([
                    'foo' => g\string(),
                    'bar' => g\int()
                ])
            )
            ->then(function (array $i) use ($type) {
                /** @var ValidationSuccess<A> $validation */
                $validation = $type->decode($i);

                self::assertInstanceOf(ValidationSuccess::class, $validation);
                self::assertInstanceOf(in\A::class, $validation->getValue());
                self::assertSame($i['foo'], $validation->getValue()->getFoo());
                self::assertSame($i['bar'], $validation->getValue()->getBar());
            });


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
                    )
                ])
            )
            ->then(function (array $i) use ($type) {
                $validation = $type->decode($i);

                self::assertInstanceOf(ValidationFailures::class, $validation);
            });
    }

    public function testUnionType(): void {
        $type = Codecs::union(
            Codecs::classFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::int()
                ],
                function (string $foo, int $bar): in\A {
                    return new in\A($foo, $bar);
                },
                in\A::class
            ),
            Codecs::null()
        );

        $this
            ->forAll(
                g\associative([
                    'foo' => g\string(),
                    'bar' => g\int()
                ])
            )
            ->then(function (array $i) use ($type) {
                self::asserSuccessInstanceOf(
                    in\A::class,
                    $type->decode($i),
                    function (in\A $a) use ($i) {
                        self::assertSame($i['foo'], $a->getFoo());
                        self::assertSame($i['bar'], $a->getBar());
                    }
                );
            });

        $this
            ->forAll(
                g\oneOf(
                    g\associative([
                        'foo' => g\string(),
                        'bar' => g\int()
                    ]),
                    g\constant(null)
                )
            )
            ->then(function ($i) use ($type) {
                $validation = $type->decode($i);
                self::assertInstanceOf(ValidationSuccess::class, $validation);
            });
    }

    public function testListType(): void
    {
        $type = Codecs::listt(
            Codecs::classFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::int()
                ],
                function(string $f, int $b): in\A {
                    return new in\A($f, $b);
                },
                in\A::class
            )
        );

        $this
            ->forAll(
                g\bind(
                    g\choose(3, 10),
                    function (int $size): g
                    {
                        return g\vector(
                            $size,
                            g\associative([
                                'foo' => g\string(),
                                'bar' => g\int()
                            ])
                        );
                    }
                )
            )
            ->then(function (array $list) use ($type): void {
                self::asserSuccessAnd(
                    $type->decode($list),
                    function (array $decoded) use ($list): void {
                        self::assertCount(count($list), $decoded);
                        self::assertContainsOnly(in\A::class, $decoded);
                    }
                );
            });
    }

    public function testComposition(): void
    {
        $type = Codecs::pipe(
            Codecs::string(),
            new RegexType('/^foo:(?<foo>\w{2,5})#bar:(?<bar>\d{1,3})$/'),
            Codecs::classFromArray(
                [
                    'foo' => Codecs::string(),
                    'bar' => Codecs::pipe(Codecs::string(), Codecs::intFromString())
                ],
                [in\A::class, 'create'],
                in\A::class
            )
        );

        self::asserSuccessInstanceOf(
            in\A::class,
            $type->decode('foo:abc#bar:123')
        );
    }
}

namespace Tests\Pybatt\Codec\in;

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
