<?php

declare(strict_types=1);

namespace TypeAssertions\Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\Validation;
use TypeAssertions\Facile\PhpCodec\TypeAssertion;

class ArrayPropsDecoderTest extends TypeAssertion
{
    public function test(): void
    {
        /** psalm-trace $d */
        $d = Decoders::arrayProps([
            'a' => Decoders::string(),
            'b' => Decoders::int(),
        ]);

        /** @var mixed $i */
        $i = null;

        $v = $d->decode($i);

        /**
         * @psalm-type K = 'a' | 'b'
         * @psalm-type V = string | int
         * @psalm-param  Validation<non-empty-array<K, V>> $v
         */
        $assert1 = function (Validation $v): void {
        };

        $assert1($v);
    }
}
