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
     * @psalm-param T $a
     * @psalm-return ValidationSuccess<T>
     */
    public static function success($a): self
    {
        return new ValidationSuccess($a);
    }

    /**
     * @template T
     * @psalm-param list<VError> $errors
     * @psalm-return ValidationFailures<T>
     *
     * @param VError[] $errors
     */
    public static function failures(array $errors): self
    {
        return new ValidationFailures($errors);
    }

    /**
     * @template T
     * @psalm-param mixed $value
     * @psalm-param Context $context
     * @psalm-param string|null $message
     * @psalm-return ValidationFailures<T>
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
     * @psalm-param callable(T):R $onSuccess
     * @psalm-param Validation<T> $v
     * @psalm-return R
     */
    public static function fold(callable $onFailures, callable $onSuccess, self $v)
    {
        if ($v instanceof ValidationSuccess) {
            /** @var ValidationSuccess<T> $v */
            return $onSuccess($v->getValue());
        }

        if ($v instanceof ValidationFailures) {
            /** @var ValidationFailures<T> $v */
            return $onFailures($v->getErrors());
        }

        throw new \LogicException('unknown type');
    }

    /**
     * @template T
     * @template R
     *
     * @psalm-param Validation<T> $v
     * @psalm-param callable(T):R $onSuccess
     * @psalm-param callable(list<VError>):R $onFailures
     * @psalm-return R
     */
    public static function fold2(self $v, callable $onSuccess, callable $onFailures)
    {
        if (self::isSuccess($v)) {
            return $onSuccess($v->getValue());
        }

        if (self::isFailures($v)) {
            return $onFailures($v->getErrors());
        }

        throw new \LogicException('unknown type');
    }

    /**
     * @template T
     * @psalm-param Validation<T> $v
     * @psalm-assert-if-true ValidationSuccess<T> $v
     *
     */
    private static function isSuccess(self $v): bool
    {
        return $v instanceof ValidationSuccess;
    }

    /**
     * @template T
     * @psalm-param Validation<T> $v
     * @psalm-assert-if-true ValidationFailures<T> $v
     *
     */
    private static function isFailures(self $v): bool
    {
        return $v instanceof ValidationFailures;
    }

    /**
     * @template T
     * @psalm-param list<Validation<T>> $validations
     * @psalm-return Validation<list<T>>
     */
    public static function sequence(array $validations): self
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

        return self::success($results);
    }

    /**
     * @template T
     * @psalm-param list<Validation<T>> $validations
     * @psalm-return Validation<list<T>>
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
                /** @var ValidationFailures<T> $v */
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
     * @psalm-param Validation<T1> $v
     * @psalm-return Validation<T2>
     */
    public static function map(callable $f, self $v): self
    {
        if (self::isSuccess($v)) {
            return self::success($f($v->getValue()));
        }

        /** @var ValidationFailures<T2> $v */
        return $v;
    }

    /**
     * @template T1
     * @template T2
     *
     * @psalm-param callable(T1):Validation<T2> $f
     * @psalm-param Validation<T1> $v
     * @psalm-return Validation<T2>
     */
    public static function bind(callable $f, self $v): self
    {
        if ($v instanceof ValidationSuccess) {
            /** @var ValidationSuccess<T1> $v */
            return $f($v->getValue());
        }

        /** @var ValidationFailures<T2> $v */
        return $v;
    }
}
