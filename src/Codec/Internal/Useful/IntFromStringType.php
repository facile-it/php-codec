<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Useful;

use Pybatt\Codec\Encode;
use Pybatt\Codec\Internal\PreconditionFailureExcepion;
use Pybatt\Codec\Internal\Primitives\IntRefine;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\Validation;

/**
 * @extends Type<int, string, int>
 */
class IntFromStringType extends Type
{
    public function __construct()
    {
        parent::__construct('IntFromString', new IntRefine(), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return is_numeric($i)
            ? Validation::success((int)$i)
            : Validation::failure($i, $context);
    }

    protected function forceCheckPrecondition($i)
    {
        if(!is_string($i)) {
            throw PreconditionFailureExcepion::create('string', $i);
        }

        return $this;
    }
}
