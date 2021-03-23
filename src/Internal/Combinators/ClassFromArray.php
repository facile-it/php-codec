<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Combinators;

use Facile\Codec\Codec;
use Facile\Codec\Internal\Arrays\MapRefiner;
use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\PreconditionFailureExcepion;
use Facile\Codec\Internal\Primitives\InstanceOfRefiner;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\ContextEntry;
use Facile\Codec\Validation\Validation;
use function Facile\Codec\destructureIn;
use function Facile\Codec\Internal\nameFromProps;

/**
 * @template T
 * @extends Type<T, array<array-key, mixed>, T>
 */
class ClassFromArray extends Type
{
    /** @var callable(...mixed):T */
    private $builder;
    /** @var non-empty-array<array-key, Codec> */
    private $props;

    /**
     * @param non-empty-array<array-key, Codec> $props
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
            new InstanceOfRefiner($fqcn),
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

    public function forceCheckPrecondition($i)
    {
        if(!(new MapRefiner())->is($i)) {
            throw PreconditionFailureExcepion::create('array<string, mixed>', $i);
        }

        return $this;
    }
}
