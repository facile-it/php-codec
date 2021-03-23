<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Useful;

use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\Primitives\InstanceOfRefiner;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;

/**
 * @extends Type<\DateTime, string, \DateTime>
 */
class DateTimeFromIsoStringType extends Type
{
    public function __construct()
    {
        parent::__construct(
            'DateFromATOMString',
            new InstanceOfRefiner(\DateTime::class),
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
