<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

/**
 * @template A
 */
abstract class Validation
{
    /**
     * @psalm-template T
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
     * @psalm-template T
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
     * @psalm-template T
     * @psalm-param mixed $value
     * @psalm-param Context $context
     * @psalm-param string|null $message
     * @psalm-return ValidationFailures<T>
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
     * @psalm-template T
     * @psalm-template R
     *
     * @psalm-param callable(list<VError>):R $onFailures
     * @psalm-param callable(T):R $onSuccess
     * @psalm-param Validation<T> $v
     * @psalm-return R
     */
    public static function fold(callable $onFailures, callable $onSuccess, self $v)
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
     * @psalm-template T
     * @psalm-param Validation<T> $v
     * @psalm-assert-if-true ValidationSuccess<T> $v
     */
    private static function isSuccess(self $v): bool
    {
        return $v instanceof ValidationSuccess;
    }

    /**
     * @psalm-template T
     * @psalm-param Validation<T> $v
     * @psalm-assert-if-true ValidationFailures<T> $v
     */
    private static function isFailures(self $v): bool
    {
        return $v instanceof ValidationFailures;
    }

    /**
     * @psalm-template T
     * @psalm-param list<Validation<T>> $validations
     * @psalm-return Validation<list<T>>
     *
     * @deprecated use sequence in ListOfValidation
     * @see ListOfValidation::sequence()
     */
    public static function sequence(array $validations): self
    {
        return ListOfValidation::sequence($validations);
    }

    /**
     * @psalm-template T
     * @psalm-param list<Validation<T>> $validations
     * @psalm-return Validation<list<T>>
     *
     * @deprecated use ListOfValidation instead
     * @see ListOfValidation::reduceToSuccessOrAllFailures()
     */
    public static function reduceToSuccessOrAllFailures(array $validations): self
    {
        return ListOfValidation::reduceToSuccessOrAllFailures($validations);
    }

    /**
     * @psalm-template T1
     * @psalm-template T2
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
     * @psalm-template T1
     * @psalm-template T2
     *
     * @psalm-param callable(T1):Validation<T2> $f
     * @psalm-param Validation<T1> $v
     * @psalm-return Validation<T2>
     */
    public static function bind(callable $f, self $v): self
    {
        if (self::isSuccess($v)) {
            return $f($v->getValue());
        }

        /** @var ValidationFailures<T2> $v */
        return $v;
    }
}
