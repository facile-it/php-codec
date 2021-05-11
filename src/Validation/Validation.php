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
     *
     * @param mixed $a
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
     *
     * @return ValidationFailures
     */
    public static function failures(array $errors): self
    {
        return new ValidationFailures($errors);
    }

    /**
     * @template V
     * @psalm-param V $value
     * @psalm-param Context $context
     * @psalm-param string|null $message
     * @psalm-return Validation<empty>
     *
     * @param mixed $value
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
            return $onFailures($v->getErrors());
        }

        throw new \LogicException('unknown type');
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
                /** @var ValidationFailures<empty> */
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
     * @psalm-param Validation<T1> $v
     * @psalm-return Validation<T2>
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
     * @psalm-param Validation<T1> $v
     * @psalm-return Validation<T2>
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
