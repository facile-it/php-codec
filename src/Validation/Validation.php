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
     * @param T $a
     *
     * @return ValidationSuccess<T>
     */
    public static function success($a): self
    {
        return new ValidationSuccess($a);
    }

    /**
     * @template T
     *
     * @param list<VError> $errors
     *
     * @return ValidationFailures<T>
     */
    public static function failures(array $errors): self
    {
        return new ValidationFailures($errors);
    }

    /**
     * @template T
     *
     * @param mixed $value
     *
     * @return ValidationFailures<T>
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
     * @param callable(list<VError>):R $onFailures
     * @param callable(T):R            $onSuccess
     * @param Validation<T>            $v
     *
     * @return R
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
     * @template T
     *
     * @param Validation<T> $v
     *
     * @phpstan-assert-if-true ValidationSuccess<T> $v
     */
    private static function isSuccess(self $v): bool
    {
        return $v instanceof ValidationSuccess;
    }

    /**
     * @template T
     *
     * @param Validation<T> $v
     *
     * @phpstan-assert-if-true ValidationFailures<T> $v
     */
    private static function isFailures(self $v): bool
    {
        return $v instanceof ValidationFailures;
    }

    /**
     * @template T
     *
     * @param list<Validation<T>> $validations
     *
     * @return Validation<list<T>>
     *
     * @deprecated use sequence in ListOfValidation
     * @see ListOfValidation::sequence()
     */
    public static function sequence(array $validations): self
    {
        return ListOfValidation::sequence($validations);
    }

    /**
     * @template T
     *
     * @param list<Validation<T>> $validations
     *
     * @return Validation<list<T>>
     *
     * @deprecated use ListOfValidation instead
     * @see ListOfValidation::reduceToSuccessOrAllFailures()
     */
    public static function reduceToSuccessOrAllFailures(array $validations): self
    {
        return ListOfValidation::reduceToSuccessOrAllFailures($validations);
    }

    /**
     * @template T1
     * @template T2
     *
     * @param callable(T1):T2 $f
     * @param Validation<T1>  $v
     *
     * @return Validation<T2>
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
     * @param callable(T1):Validation<T2> $f
     * @param Validation<T1>              $v
     *
     * @return Validation<T2>
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
