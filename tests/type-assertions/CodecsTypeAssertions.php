<?php

declare(strict_types=1);

namespace TypeAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Validation\Validation;

class CodecsTypeAssertions extends TypeAssertion
{
    public function testPipe(): void
    {
        $c2 = Codecs::pipe(
            Codecs::mixed(),
            Codecs::string()
        );

        Validation::fold(
            function (): void {
            },
            [self::class, 'assertString'],
            $c2->decode('hello')
        );

        $c3 = Codecs::pipe(
            Codecs::mixed(),
            Codecs::string(),
            Codecs::intFromString()
        );

        Validation::fold(
            function (): void {
            },
            [self::class, 'assertInt'],
            $c3->decode('123')
        );
    }
}
