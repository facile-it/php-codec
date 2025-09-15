<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @psalm-template A
 *
 * @extends Validation<A>
 */
final class ValidationFailures extends Validation
{
    /** @var list<VError> */
    private array $errors;

    /**
     * @param VError[] $errors
     *
     * @psalm-param list<VError> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return VError[]
     *
     * @psalm-return list<VError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
