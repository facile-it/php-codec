<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Arrays;

use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @extends Type<array<array-key,mixed>, mixed, array<array-key,mixed>>
 */
class MapType extends Type
{
    public function __construct()
    {
        parent::__construct(
            'array',
            new MapRefiner(),
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
