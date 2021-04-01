<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @template A
 * @extends Validation<A>
 */
class ValidationFailures extends Validation
{
    /** @var list<VError> */
    private $errors;

    /**
     * @param list<VError> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return list<VError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
