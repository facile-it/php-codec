<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Arrays;

use Pybatt\Codec\Internal\Encode;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\Validation;

/**
 * @extends Type<array<array-key,mixed>, mixed, array<array-key,mixed>>
 */
class MapType extends Type
{
    public function __construct()
    {
        parent::__construct(
            'array',
            new MapRefine(),
            Encode::identity()
        );
    }

    public function validate($i, Context $context): Validation
    {
        if($this->is($i)) {
            /** @var array<array-key, mixed> $i */
            return Validation::success($i);
        }

        return Validation::failure($i, $context);
    }
}
