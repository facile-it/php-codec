<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;

/**
 * @param non-empty-array<array-key, Codec> $props
 */
function nameFromProps(array $props): string
{
    return \sprintf(
        '{%s}',
        \implode(
            ', ',
            \array_map(
                static function (Codec $t, $k): string {
                    return \sprintf(
                        '%s: %s',
                        \is_string($k) ? $k : \sprintf('[%d]', $k),
                        $t->getName()
                    );
                },
                $props,
                \array_keys($props)
            )
        )
    );
}

/**
 * @param mixed $x
 */
function typeof($x): string
{
    if (\is_object($x)) {
        return \get_class($x);
    }

    return \gettype($x);
}

/**
 * @template A
 * @template I
 *
 * @param Decoder<I, A> $decoder
 * @param I             $input
 *
 * @return Validation<A>
 */
function standardDecode(Decoder $decoder, $input): Validation
{
    return $decoder->validate(
        $input,
        new Context(
            new ContextEntry('', $decoder, $input)
        )
    );
}
