<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Useful;

use Pybatt\Codec\Encode;
use Pybatt\Codec\Internal\Primitives\InstanceOfRefine;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\Validation;

/**
 * @extends Type<\DateTime, string, \DateTime>
 */
class DateTimeFromIsoStringType extends Type
{
    public function __construct()
    {
        parent::__construct(
            'DateFromATOMString',
            new InstanceOfRefine(\DateTime::class),
            Encode::identity()
        );
    }

    public function validate($i, Context $context): Validation
    {
        $r = \DateTime::createFromFormat(DATE_ATOM, $i);

        if($r === false) {
            return Validation::failure($i, $context);
        }

        return $this->is($r)
            ? Validation::success($r)
            : Validation::failure($i, $context);
    }
}
