<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Experimental;

use Pybatt\Codec\Internal\Encode;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\ContextEntry;
use Pybatt\Codec\Validation\Validation;
use function Pybatt\Codec\Internal\nameFromProps;

/**
 * @extends Type<array<array-key, mixed>, mixed, array<array-key, mixed>>
 */
class AssociativeArrayType extends Type
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
            new AssociativeArrayRefiner($props),
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
