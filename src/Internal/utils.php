<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-param non-empty-array<array-key, Decoder> $props
 *
 * @param Decoder[] $props
 */
function nameFromProps(array $props): string
{
    return \sprintf(
        '{%s}',
        \implode(
            ', ',
            \array_map(
                static function (Decoder $t, $k): string {
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
 * @psalm-template A
 * @psalm-template I
 * @psalm-param Decoder<I, A> $decoder
 * @psalm-param I             $input
 * @psalm-return Validation<A>
 *
 * @param mixed $input
 */
function standardDecode(Decoder $decoder, $input): Validation
{
    return $decoder->validate(
        $input,
        new Context(
            $decoder,
            new ContextEntry('', $decoder, $input)
        )
    );
}
