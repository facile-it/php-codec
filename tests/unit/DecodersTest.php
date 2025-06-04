<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;

/** @psalm-suppress PropertyNotSetInConstructor */
class DecodersTest extends BaseTestCase
{
    use TestTrait;

    public function testMap(): void
    {
        $decoder = Decoders::transformValidationSuccess(
            fn(int $v): DecodersTest\A => new DecodersTest\A($v),
            Decoders::int()
        );

        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                Generators::int() //provare il decoder con molti interi casuali
            )
            ->then(function (int $i) use ($decoder): void {
                $a = self::assertSuccessInstanceOf(
                    DecodersTest\A::class,
                    $decoder->decode($i)
                );
                self::assertSame($i, $a->getValue());//verifica che il valore dentro l'oggetto A sia uguale a $i
            });
    }
}

namespace Tests\Facile\PhpCodec\DecodersTest;

//wrapper class usata per testare la trasformazione: prende un int e lo incapsula in un oggetto
class A
{
    private int $v;

    public function __construct(int $v)
    {
        $this->v = $v;
    }

    public function getValue(): int
    {
        return $this->v;
    }
}
