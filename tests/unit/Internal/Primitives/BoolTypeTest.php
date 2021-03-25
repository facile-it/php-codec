<?php

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Internal\Primitives\BoolType;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

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
