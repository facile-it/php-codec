<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Useful;

use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\PreconditionFailureExcepion;
use Facile\Codec\Internal\Primitives\IntRefiner;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;

/**
 * @extends Type<int, string, int>
 */
class IntFromStringType extends Type
{
    public function __construct()
    {
        parent::__construct('IntFromString', new IntRefiner(), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return is_numeric($i)
            ? Validation::success((int)$i)
            : Validation::failure($i, $context);
    }

    public function forceCheckPrecondition($i)
    {
        if(!is_string($i)) {
            throw PreconditionFailureExcepion::create('string', $i);
        }

        return $this;
    }
}
