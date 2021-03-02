<?php declare(strict_types=1);

namespace Pybatt\Codec;

/**
 * @template A
 * @extends Validation<A>
 */
class ValidationFailures extends Validation
{
    /** @var list<ValidationError> */
    private $errors;

    /**
     * @param list<ValidationError> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return list<ValidationError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
