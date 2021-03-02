<?php declare(strict_types=1);

namespace Pybatt\Codec\CommonTypes;

use Pybatt\Codec\Context;
use Pybatt\Codec\Encode;
use Pybatt\Codec\Refiners\RefineAssociativeArrayWithStringKeys;
use Pybatt\Codec\Type;
use Pybatt\Codec\Validation;
use function Pybatt\Codec\destructureIn;
use function Pybatt\Codec\nameFromProps;

/**
 * @template T
 * @extends Type<T, array<array-key, mixed>, T>
 */
class ClassFromArray extends Type
{
    /** @var callable(...mixed):T */
    private $builder;
    /** @var non-empty-array<string, Type> */
    private $props;

    /**
     * @param non-empty-array<string, Type> $props
     * @param callable(...mixed):T $builder
     */
    public function __construct(
        array $props,
        callable $builder
    )
    {
        parent::__construct(
            nameFromProps($props),
            new RefineAssociativeArrayWithStringKeys($props),
            Encode::identity()
        );

        $this->builder = $builder;
        $this->props = $props;
    }

    public function validate($i, Context $context): Validation
    {
        if ($this->is($i)) {
            $params = [];

            foreach ($this->props as $k => $v) {
                $params[] = $v->validate($i[$k], $context);
            }

            return Validation::map(
                destructureIn($this->builder),
                Validation::sequence($params)
            );
        }

        return Validation::failure($i, $context);
    }
}
