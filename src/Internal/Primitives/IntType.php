<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @extends Type<int, mixed, int>
 */
class IntType extends Type
{
    public function __construct()
    {
        parent::__construct('int', new IntRefiner(), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }
}


