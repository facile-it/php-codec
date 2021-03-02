<?php declare(strict_types=1);

namespace Pybatt\Codec\CommonTypes;

use Pybatt\Codec\Context;
use Pybatt\Codec\ContextEntry;
use Pybatt\Codec\Encode;
use Pybatt\Codec\Refiners\RefineMap;
use Pybatt\Codec\Type;
use Pybatt\Codec\Validation;
use function Pybatt\Codec\nameFromProps;

/**
 * @extends Type<array<array-key, mixed>, mixed, array<array-key, mixed>>
 */
class MapType extends Type
{
    /** @var non-empty-array<string, Type> */
    private $props;

    /**
     * @param non-empty-array<string, Type> $props
     */
    public function __construct(array $props)
    {
        parent::__construct(
            nameFromProps($props),
            new RefineMap($props),
            Encode::identity()
        );
        $this->props = $props;
    }

    public function validate($i, Context $context): Validation
    {
        if ($this->is($i)) {
            return Validation::success($i);
        }

        foreach ($this->props as $k => $v) {
            if (isset($i[$k]) && !$v->is($i[$k])) {
                $context = $context->appendEntries(
                    new ContextEntry($k, $v, $i[$k])
                );
            }
        }

        return Validation::failure($i, $context);
    }
}
