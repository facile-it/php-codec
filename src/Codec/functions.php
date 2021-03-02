<?php declare(strict_types=1);

namespace Pybatt\Codec;

/**
 * @const callable
 */
const identity = __NAMESPACE__ . '\identity';

/**
 * @template A
 * @param A $x
 * @return A
 */
function identity($x)
{
    return $x;
}

/**
 * @param mixed $x
 * @return string
 */
function strigify($x): string
{
    if(is_array($x)) {
        // TODO check if json-ext is available
        return json_encode($x);
    }

    if(is_bool($x)) {
        return $x ? 'true' : 'false';
    }

    return (string)$x;
}

/**
 * @param non-empty-array<string, Type> $props
 * @return string
 * @psalm-pure
 */
function nameFromProps(array $props): string
{
    return sprintf(
        '{%s}',
        implode(
            ', ',
            array_map(
                static function (Type $t, string $k): string {
                    return sprintf(
                        '%s: %s',
                        $k,
                        $t->getName()
                    );
                },
                $props,
                array_keys($props)
            )
        )
    );
}

/**
 * @template R
 * @param callable(...mixed):R $f
 * @return callable(list<mixed>):R
 */
function destructureIn(callable $f): callable
{
    return function(array $params) use ($f) {
        return $f(...$params);
    };
}
