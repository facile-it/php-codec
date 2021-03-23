<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Primitives;

use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;

/**
 * @extends Type<string, mixed, string>
 */
class StringType extends Type
{
    public function __construct()
    {
        parent::__construct('string', new StringRefiner(), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }
}
