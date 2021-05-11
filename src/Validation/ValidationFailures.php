<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @psalm-template A
 * @extends Validation<A>
 */
final class ValidationFailures extends Validation
{
    /** @var list<VError> */
    private $errors;

    /**
     * @psalm-param list<VError> $errors
     *
     * @param VError[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
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
