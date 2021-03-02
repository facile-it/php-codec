<?php declare(strict_types=1);

namespace Pybatt\Codec\Primitives;

use Pybatt\Codec\Context;
use Pybatt\Codec\Encode;
use Pybatt\Codec\Type;
use Pybatt\Codec\Validation;

/**
 * @extends Type<float, mixed, float>
 */
class FloatType extends Type
{
    public function __construct()
    {
        parent::__construct('float', new RefineFloat(), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }
}
