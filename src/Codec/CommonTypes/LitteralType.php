<?php declare(strict_types=1);

namespace Pybatt\Codec\CommonTypes;

use Pybatt\Codec\Context;
use Pybatt\Codec\Encode;
use Pybatt\Codec\Refiners\RefineLitteral;
use Pybatt\Codec\Type;
use Pybatt\Codec\Validation;
use function Pybatt\Codec\strigify;

/**
 * @extends Type<scalar, mixed, scalar>
 */
class LitteralType extends Type
{
    /**
     * @param scalar $litteral
     */
    public function __construct($litteral)
    {
        parent::__construct(strigify($litteral), new RefineLitteral($litteral), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }
}
