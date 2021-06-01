<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Codec;
use function Facile\PhpCodec\destructureIn;
use Facile\PhpCodec\Internal\Encode;
use function Facile\PhpCodec\Internal\nameFromProps;
use Facile\PhpCodec\Internal\Primitives\InstanceOfRefiner;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Internal\Undefined;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\ListOfValidation;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template T
 * @extends Type<T, array<array-key, mixed>, T>
 */
class ClassFromArray extends Type
{
    /** @var callable(...mixed):T */
    private $builder;
    /** @var non-empty-array<array-key, Codec> */
    private $props;

    /**
     * @psalm-param non-empty-array<array-key, Codec> $props
     * @psalm-param callable(...mixed):T              $builder
     * @psalm-param class-string<T>                   $fqcn
     */
    public function __construct(
        array $props,
        callable $builder,
        string $fqcn
    ) {
        parent::__construct(
            \sprintf('%s(%s)', $fqcn, nameFromProps($props)),
            new InstanceOfRefiner($fqcn),
            Encode::identity()
        );

        $this->builder = $builder;
        $this->props = $props;
    }

    public function validate($i, Context $context): Validation
    {
        $validations = [];

        foreach ($this->props as $k => $codec) {
            $keyName = \is_string($k) ? $k : \sprintf('[%d]', $k);
            /** @var mixed $value */
            $value = \array_key_exists($k, $i) ? $i[$k] : new Undefined();

            $validations[] = $codec->validate($value, $context->appendEntries(new ContextEntry($keyName, $codec, $value)));
        }

        return Validation::map(
            destructureIn($this->builder),
            ListOfValidation::reduceToSuccessOrAllFailures($validations)
        );
    }
}
