<?php declare(strict_types=1);

namespace TypeAssertions\Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoders;
use TypeAssertions\Facile\PhpCodec\TypeAssertion;

class ArrayPropsDecoderTest extends TypeAssertion
{
    public function test(): void
    {
        /** @psalm-trace $d */
        $d = Decoders::arrayProps([
            'a' => Decoders::string(),
            'b' => Decoders::int()
        ]);

        // Why input is empty?
    }
}
