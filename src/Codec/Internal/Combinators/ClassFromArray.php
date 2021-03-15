<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Combinators;

use Pybatt\Codec\Internal\Arrays\MapRefine;
use Pybatt\Codec\Internal\Encode;
use Pybatt\Codec\Internal\PreconditionFailureExcepion;
use Pybatt\Codec\Internal\Primitives\InstanceOfRefine;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\ContextEntry;
use Pybatt\Codec\Validation\Validation;
use function Pybatt\Codec\destructureIn;
use function Pybatt\Codec\Internal\nameFromProps;

/**
 * @template T
 * @extends Type<T, array<array-key, mixed>, T>
 */
class ClassFromArray extends Type
{
    /** @var callable(...mixed):T */
    private $builder;
    /** @var non-empty-array<array-key, Type> */
    private $props;

    /**
     * @param non-empty-array<array-key, Type> $props
     * @param callable(...mixed):T $builder
     * @param class-string<T> $fqcn
     */
    public function __construct(
        array $props,
        callable $builder,
        string $fqcn
    )
    {
        parent::__construct(
            sprintf('%s(%s)', $fqcn, nameFromProps($props)),
            new InstanceOfRefine($fqcn),
            Encode::identity()
        );

        $this->builder = $builder;
        $this->props = $props;
    }

    public function validate($i, Context $context): Validation
    {
        $validations = [];

        foreach ($this->props as $k => $v) {
            $key = is_string($k) ? $k : sprintf('[%d]', $k);

            if (array_key_exists($k, $i)) {
                $validations[] = $v->validate($i[$k], $context->appendEntries(new ContextEntry($key, $v, $i[$k])));
            } else {
                $validations[] = Validation::failure(
                    ContextEntry::VALUE_UNDEFINED,
                    $context->appendEntries(new ContextEntry($key, $v, ContextEntry::VALUE_UNDEFINED))
                );
            }
        }

        return Validation::map(
            destructureIn($this->builder),
            Validation::reduceToSuccessOrAllFailures($validations)
        );
    }

    protected function forceCheckPrecondition($i)
    {
        if(!(new MapRefine())->is($i)) {
            throw PreconditionFailureExcepion::create('array<string, mixed>', $i);
        }

        return $this;
    }
}
