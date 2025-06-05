<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Facile\PhpCodec\Internal\Primitives\StringDecoder;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ValidationSuccess;

final class StringDecoderTest extends TestCase
{
    public function testValidString(): void
    {
        $decoder = new StringDecoder();
        $context = new Context($decoder); //cosa controllare

        //il valore da validare passa il controllo?
        $result = $decoder->validate('ciao', $context);
        //è ciò che mi aspettavo? (Successo)
        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    public function testInvalidValues(): void
    {
        $decoder = new StringDecoder();
        $context = new Context($decoder);

        foreach ([123, 3.14, true] as $input) {
            $result = $decoder->validate($input, $context);
            $this->assertNotInstanceOf(ValidationSuccess::class, $result);
        }
    }
}
