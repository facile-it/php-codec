<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template T of bool | string | int
 * @extends Type<T, mixed, T>
 */
class LitteralType extends Type
{
    /**
     * @param T $litteral
     */
    public function __construct($litteral)
    {
        parent::__construct(
            self::litteralName($litteral),
            new LitteralRefiner($litteral),
            Encode::identity()
        );
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }

    /**
     * @param int | bool | string $x
     * @return string
     */
    private static function litteralName($x): string
    {
        if(is_string($x)) {
            return "'$x'";
        }

        if(is_bool($x)) {
            return $x ? 'true' : 'false';
        }

        return (string)$x;
    }
}
