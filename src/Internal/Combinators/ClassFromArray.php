<?php declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Internal\Arrays\MapRefiner;
use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\PreconditionFailureExcepion;
use Facile\PhpCodec\Internal\Primitives\InstanceOfRefiner;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;
use function Facile\PhpCodec\destructureIn;
use function Facile\PhpCodec\Internal\nameFromProps;

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
