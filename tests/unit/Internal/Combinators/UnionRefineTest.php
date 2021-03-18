<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Internal\Combinators;

use Eris\Generator;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Internal\Combinators\UnionRefiner;
use Pybatt\Codec\Internal\Experimental\AssociativeArrayRefiner;
use Pybatt\Codec\Internal\Primitives\IntRefiner;
use Pybatt\Codec\Internal\Primitives\NullRefiner;
use Pybatt\Codec\Internal\Primitives\StringRefiner;

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
            ->then(function($x) use ($mapOrNull, $stringOrInt) {
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
            ->then(function($x) use ($stringOrNull) {
                self::assertTrue($stringOrNull->is($x));
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\constant(null),
                    Generator\int()
                )
            )
            ->then(function($x) use ($intOrNull) {
                self::assertTrue($intOrNull->is($x));
            });

        $this
            ->forAll(
                Generator\oneOf(
                    Generator\constant(null),
                    Generator\associative([
                        'a' => Generator\string(),
                        'b' => Generator\int()
                    ])
                )
            )
            ->then(function($x) use ($stringOrInt, $mapOrNull) {
                self::assertTrue($mapOrNull->is($x));
                self::assertFalse($stringOrInt->is($x));
            });
    }
}
