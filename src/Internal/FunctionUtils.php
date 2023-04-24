<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;

final class FunctionUtils
{
    /**
     * @psalm-param non-empty-array<array-key, Decoder> $props
     *
     * @param Decoder[] $props
     */
    public static function nameFromProps(array $props): string
    {
        return \sprintf(
            '{%s}',
            \implode(
                ', ',
                \array_map(
                    static fn (Decoder $t, $k): string => \sprintf(
                        '%s: %s',
                        \is_string($k) ? $k : \sprintf('[%d]', $k),
                        $t->getName()
                    ),
                    $props,
                    \array_keys($props)
                )
            )
        );
    }

    /**
     * @psalm-template A
     * @psalm-template I
     *
     * @psalm-param Decoder<I, A> $decoder
     * @psalm-param I             $input
     *
     * @psalm-return Validation<A>
     *
     * @param mixed $input
     */
    public static function standardDecode(Decoder $decoder, $input): Validation
    {
        return $decoder->validate(
            $input,
            new Context(
                $decoder,
                new ContextEntry('', $decoder, $input)
            )
        );
    }

    /**
     * @psalm-template R
     *
     * @psalm-param callable(mixed...):R $f
     *
     * @psalm-return callable(list<mixed>):R
     */
    public static function destructureIn(callable $f): callable
    {
        return fn (array $params) => $f(...$params);
    }

    /**
     * @param mixed $x
     *
     * @return string
     */
    public static function strigify($x): string
    {
        if ($x === null) {
            return 'null';
        }

        if (\is_string($x)) {
            return "\"$x\"";
        }

        if (\is_array($x)) {
            return \function_exists('json_encode')
                ? \json_encode($x, JSON_THROW_ON_ERROR)
                : \serialize($x);
        }

        if (\is_bool($x)) {
            return $x ? 'true' : 'false';
        }

        return (string) $x;
    }
}
