<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Internal\Combinators;

use Eris\Generator;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Internal\Arrays\AssociativeArrayRefine;
use Pybatt\Codec\Internal\Combinators\UnionRefine;
use Pybatt\Codec\Internal\Primitives\IntRefine;
use Pybatt\Codec\Internal\Primitives\NullRefine;
use Pybatt\Codec\Internal\Primitives\StringRefine;

class UnionRefineTest extends TestCase
{
    use TestTrait;

    public function testRefineUnion(): void
    {
        $stringOrInt = new UnionRefine(new StringRefine(), new IntRefine());
        $stringOrNull = new UnionRefine(new StringRefine(), new NullRefine());
        $intOrNull = new UnionRefine(new IntRefine(), new NullRefine());
        $mapOrNull = new UnionRefine(new AssociativeArrayRefine(['a' => new StringRefine(), 'b' => new IntRefine()]), new NullRefine());

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
