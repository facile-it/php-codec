<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Eris\Generator;
use Eris\TestTrait;
use Facile\PhpCodec\Internal\Combinators\UnionRefiner;
use Facile\PhpCodec\Internal\Experimental\AssociativeArrayRefiner;
use Facile\PhpCodec\Internal\Primitives\IntRefiner;
use Facile\PhpCodec\Internal\Primitives\NullRefiner;
use Facile\PhpCodec\Internal\Primitives\StringRefiner;
use PHPUnit\Framework\TestCase;

class UnionRefineTest extends TestCase
{
    use TestTrait;

    public function testRefineUnion(): void
    {
        $stringOrInt = new UnionRefiner(new StringRefiner(), new IntRefiner());
        $stringOrNull = new UnionRefiner(new StringRefiner(), new NullRefiner());
        $intOrNull = new UnionRefiner(new IntRefiner(), new NullRefiner());
        $mapOrNull = new UnionRefiner(new AssociativeArrayRefiner(['a' => new StringRefiner(), 'b' => new IntRefiner()]), new NullRefiner());

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\int(),
                    Generator\string()
                )
            )
            ->then(function ($x) use ($mapOrNull, $stringOrInt): void {
                self::assertTrue($stringOrInt->is($x));
                self::assertFalse($mapOrNull->is($x));
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\constant(null),
                    Generator\string()
                )
            )
            ->then(function ($x) use ($stringOrNull): void {
                self::assertTrue($stringOrNull->is($x));
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\constant(null),
                    Generator\int()
                )
            )
            ->then(function ($x) use ($intOrNull): void {
                self::assertTrue($intOrNull->is($x));
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\constant(null),
                    Generator\associative([
                        'a' => Generator\string(),
                        'b' => Generator\int(),
                    ])
                )
            )
            ->then(function ($x) use ($stringOrInt, $mapOrNull): void {
                self::assertTrue($mapOrNull->is($x));
                self::assertFalse($stringOrInt->is($x));
            });
    }
}
