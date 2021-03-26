<?php declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Eris\TestTrait;
use Eris\Generator as g;
use Facile\PhpCodec\Internal\Primitives\UndefinedType;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Validation\ValidationFailures;
use PHPUnit\Framework\TestCase;
use Tests\Facile\PhpCodec\BaseTestCase;

class UndefinedTypeTest extends BaseTestCase
{
    use TestTrait;

    public function testDecode(): void
    {
        $undefined = new UndefinedType();

        self::asserSuccessInstanceOf(
            Undefined::class,
            $undefined->decode(new Undefined())
        );

        $this
            ->forAll(
                g\oneOf(
                    g\string(),
                    g\int(),
                    g\float(),
                    g\date(),
                    g\constant(null)
                )
            )
            ->then(function($x) use ($undefined): void {
                $v = $undefined->decode($x);
                self::assertInstanceOf(ValidationFailures::class, $v);
            });
    }
}
