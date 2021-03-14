<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal;

class PreconditionFailureExcepion extends \LogicException
{
    /**
     * @param string $expectedType
     * @param mixed $given
     * @return self
     */
    public static function create(string $expectedType, $given): self
    {
        return new self(
            sprintf(
                'Bad codec composition: expecting input to be of type "%s", given "%s"',
                $expectedType,
                typeof($given)
            )
        );
    }
}
