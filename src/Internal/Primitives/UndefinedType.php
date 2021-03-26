<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @extends Type<Undefined, mixed, Undefined>
 */
class UndefinedType extends Type
{
    public function __construct()
    {
        parent::__construct('string', new InstanceOfRefiner(Undefined::class), Encode::identity());
    }

    public function validate($i, Context $context): Validation
    {
        return $this->is($i)
            ? Validation::success($i)
            : Validation::failure($i, $context);
    }
}
