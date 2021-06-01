<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

final class ListOfValidation
{
    public function __construct()
    {
    }

    /**
     * @psalm-template T
     * @psalm-param list<Validation<T>> $validations
     * @psalm-return Validation<list<T>>
     */
    public static function sequence(array $validations): Validation
    {
        $results = [];
        foreach ($validations as $v) {
            if ($v instanceof ValidationSuccess) {
                /** @var ValidationSuccess<T> $v */
                $results[] = $v->getValue();
            } else {
                /** @var ValidationFailures<T> */
                return $v;
            }
        }

        return Validation::success($results);
    }
}
