<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @extends Type<null, mixed, null>
 */
class NullType extends Type
{
    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }

    public function __construct()
    {
        parent::__construct('null', new NullRefiner(), Encode::identity());
    }
}
