<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Primitives\InstanceOfRefiner;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

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
        $r = \DateTime::createFromFormat(\DATE_ATOM, $i);

        if ($r === false) {
            return Validation::failure($i, $context);
        }

        return $this->is($r)
            ? Validation::success($r)
            : Validation::failure($i, $context);
    }
}
