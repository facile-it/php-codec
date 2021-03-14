<?php

namespace Tests\Pybatt\Codec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Pybatt\Codec\Internal\Primitives\BoolType;
use Pybatt\Codec\Validation\ValidationFailures;
use Tests\Pybatt\Codec\BaseTestCase;

class BoolTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testValidate(): void
    {
        $type = new BoolType();

        self::asserSuccessSameTo(true, $type->decode(true));
        self::asserSuccessSameTo(false, $type->decode(false));

        $this
            ->forAll(
                g\oneOf(g\string(), g\int(), g\float(), g\date(), g\constant(null))
            )
            ->then(function($x) use ($type) {
                self::assertInstanceOf(
                    ValidationFailures::class,
                    $type->decode($x)
                );
            });
    }

    public function testEncode(): void
    {
        $type = new BoolType();

        self::assertTrue($type->encode(true));
        self::assertFalse($type->encode(false));
    }
}
