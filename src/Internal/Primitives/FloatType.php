<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;

/**
 * @extends Type<float, mixed, float>
 */
class FloatType extends Type
{
    public function __construct()
    {
        parent::__construct('float', new FloatRefiner(), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }
}
