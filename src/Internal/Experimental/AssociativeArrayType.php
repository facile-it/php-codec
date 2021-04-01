<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Experimental;

use Facile\PhpCodec\Internal\Encode;
use function Facile\PhpCodec\Internal\nameFromProps;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\ContextEntry;
use Facile\PhpCodec\Validation\Validation;

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
            if (isset($i[$k]) && ! $v->is($i[$k])) {
                $context = $context->appendEntries(
                    new ContextEntry($k, $v, $i[$k])
                );
            }
        }

        return Validation::failure($i, $context);
    }
}
