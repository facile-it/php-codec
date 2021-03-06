<?php

declare(strict_types=1);

namespace TypeAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\Validation;

class CodecsTypeAssertions extends TypeAssertion
{
    public function testPipe(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $c2 = Codecs::pipe(
            Codecs::fromDecoder(Decoders::mixed()),
            Codecs::string()
        );

        Validation::fold(
            function (): void {
            },
            [self::class, 'assertString'],
            $c2->decode('hello')
        );

        /** @psalm-suppress DeprecatedMethod */
        $c3 = Codecs::pipe(
            Codecs::fromDecoder(Decoders::mixed()),
            Codecs::string(),
            Codecs::fromDecoder(Decoders::intFromString())
        );

        Validation::fold(
            function (): void {
            },
            [self::class, 'assertInt'],
            $c3->decode('123')
        );
    }
}
