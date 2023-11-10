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
     *
     * @psalm-param list<Validation<T>> $validations
     *
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

    /**
     * @psalm-template T
     *
     * @psalm-param list<Validation<T>> $validations
     *
     * @psalm-return Validation<list<T>>
     */
    public static function reduceToSuccessOrAllFailures(array $validations): Validation
    {
        $results = [];
        $errors = [];
        foreach ($validations as $v) {
            if ($v instanceof ValidationSuccess) {
                /** @var ValidationSuccess<T> $v */
                $results[] = $v->getValue();
            } else {
                /** @var ValidationFailures<T> $v */
                $errors[] = $v->getErrors();
            }
        }

        if (! empty($errors)) {
            return Validation::failures(\array_merge([], ...$errors));
        }

        return Validation::success($results);
    }

    /**
     * @psalm-template K of array-key
     * @psalm-template Values
     * @psalm-template Vs of non-empty-array<K, Validation<Values>>
     * @psalm-template VP of Validation<non-empty-array<K, Values>>
     *
     * @psalm-param Vs $validations
     *
     * @psalm-return VP
     */
    public static function reduceToIndexedSuccessOrAllFailures(array $validations): Validation
    {
        $results = [];
        $errors = [];

        foreach ($validations as $k => $v) {
            Validation::fold(
                function (array $es) use (&$errors): void {
                    /** @var list<list<VError>> $errors */
                    $errors[] = $es;
                },
                /**
                 * @psalm-param Values $x
                 *
                 * @param mixed $x
                 */
                function ($x) use ($k, &$results): void {
                    /** @var K $k */
                    /** @var array<K, Values> $results */
                    $results[$k] = $x;
                },
                $v
            );
        }

        if (! empty($errors)) {
            /** @var list<list<VError>> $errors */
            $failures = Validation::failures(\array_merge(...$errors));

            /** @var VP $failures */
            return $failures;
        }

        return Validation::success($results);
    }
}
