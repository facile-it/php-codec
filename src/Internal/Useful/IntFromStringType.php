<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Primitives\IntRefiner;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

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
        return \is_numeric($i)
            ? Validation::success((int) $i)
            : Validation::failure($i, $context);
    }
}
