<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Refiners;

use Eris\Generator;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Pybatt\Codec\Primitives\RefineInt;
use Pybatt\Codec\Primitives\RefineNull;
use Pybatt\Codec\Primitives\RefineString;
use Pybatt\Codec\Refiners\RefineMap;
use Pybatt\Codec\Refiners\RefineUnion;

class RefineUnionTest extends TestCase
{
    use TestTrait;

    public function testRefineUnion(): void
    {
        $stringOrInt = new RefineUnion(new RefineString(), new RefineInt());
        $stringOrNull = new RefineUnion(new RefineString(), new RefineNull());
        $intOrNull = new RefineUnion(new RefineInt(), new RefineNull());
        $mapOrNull = new RefineUnion(new RefineMap(['a' => new RefineString(), 'b' => new RefineInt()]), new RefineNull());

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
