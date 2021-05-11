<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @template A
 */
abstract class Validation
{
    /**
     * @template T
     *
     * @psalm-param T $a
     *
     * @param mixed $a
     *
     * @return Validation<T>
     */
    public static function success($a): self
    {
        return new ValidationSuccess($a);
    }

    /**
     * @template T
     *
     * @psalm-param list<VError> $errors
     *
     * @param VError[] $errors
     *
     * @return Validation<T>
     */
    public static function failures(array $errors): self
    {
        return new ValidationFailures($errors);
    }

    /**
     * @param mixed       $value
     * @param Context     $context
     * @param string|null $message
     *
     * @return Validation<empty>
     */
    public static function failure($value, Context $context, ?string $message = null): self
    {
        return self::failures(
            [new VError($value, $context, $message)]
        );
    }

    /**
     * @template T
     * @template R
     *
     * @psalm-param callable(list<VError>):R $onFailures
     *
     * @param callable $onFailures
     * @psalm-param callable(T):R $onSuccess
     *
     * @param callable      $onSuccess
     * @param Validation<T> $v
     *
     * @return R
     */
    public static function fold(callable $onFailures, callable $onSuccess, self $v)
    {
        if ($v instanceof ValidationSuccess) {
            /** @var ValidationSuccess<T> $v */
            return $onSuccess($v->getValue());
        }

        if ($v instanceof ValidationFailures) {
            return $onFailures($v->getErrors());
        }

        throw new \LogicException('unknown type');
    }

    /**
     * @template T
     *
     * @param list<Validation<T>> $validations
     *
     * @return Validation<list<T>>
     */
    public static function sequence(array $validations): self
    {
        $results = [];
        foreach ($validations as $v) {
            if ($v instanceof ValidationSuccess) {
                /** @var ValidationSuccess<T> $v */
                $results[] = $v->getValue();
            } else {
                /** @var ValidationFailures<empty> */
                return $v;
            }
        }

        return self::success($results);
    }

    /**
     * @template T
     *
     * @param list<Validation<T>> $validations
     *
     * @return Validation<list<T>>
     */
    public static function reduceToSuccessOrAllFailures(array $validations): self
    {
        $results = [];
        $errors = [];
        foreach ($validations as $v) {
            if ($v instanceof ValidationSuccess) {
                /** @var ValidationSuccess<T> $v */
                $results[] = $v->getValue();
            } else {
                /** @var ValidationFailures<empty> $v */
                $errors[] = $v->getErrors();
            }
        }

        if (! empty($errors)) {
            return self::failures(\array_merge([], ...$errors));
        }

        return self::success($results);
    }

    /**
     * @template T1
     * @template T2
     *
     * @psalm-param callable(T1):T2 $f
     *
     * @param callable       $f
     * @param Validation<T1> $v
     *
     * @return Validation<T2>
     */
    public static function map(callable $f, self $v): self
    {
        if ($v instanceof ValidationSuccess) {
            /** @var ValidationSuccess<T1> $v */
            return self::success($f($v->getValue()));
        }

        /** @var ValidationFailures<empty> $v */
        return $v;
    }

    /**
     * @template T1
     * @template T2
     *
     * @psalm-param callable(T1):Validation<T2> $f
     *
     * @param callable       $f
     * @param Validation<T1> $v
     *
     * @return Validation<T2>
     */
    public static function bind(callable $f, self $v): self
    {
        if ($v instanceof ValidationSuccess) {
            /** @var ValidationSuccess<T1> $v */
            return $f($v->getValue());
        }

        /** @var ValidationFailures<empty> $v */
        return $v;
    }
}
