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
    /**
     * @psalm-param list<VError> $errors
     *
     * @param VError[] $errors
     */
    public function __construct(private readonly array $errors)
    {
    }

    /**
     * @psalm-return list<VError>
     *
     * @return VError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
