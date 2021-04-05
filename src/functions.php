<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

/**
 * @const callable
 */
const identity = __NAMESPACE__ . '\identity';

/**
 * @template A
 *
 * @param A $x
 *
 * @return A
 */
function identity($x)
{
    return $x;
}

/**
 * @param mixed $x
 *
 * @return string
 */
function strigify($x): string
{
    if ($x === null) {
        return 'null';
    }

    if (\is_string($x)) {
        return "\"$x\"";
    }

    if (\is_array($x)) {
        return \function_exists('json_encode')
            ? \json_encode($x)
            : \serialize($x);
    }

    if (\is_bool($x)) {
        return $x ? 'true' : 'false';
    }

    return (string) $x;
}

/**
 * @template R
 *
 * @param callable(...mixed):R $f
 *
 * @return callable(list<mixed>):R
 */
function destructureIn(callable $f): callable
{
    return function (array $params) use ($f) {
        return $f(...$params);
    };
}
