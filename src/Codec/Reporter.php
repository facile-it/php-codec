<?php declare(strict_types=1);

namespace Pybatt\Codec;

use Pybatt\Codec\Validation\Validation;

/**
 * @template A
 */
interface Reporter
{
    /**
     * @param Validation<mixed> $validation
     * @return A
     */
    public function report(Validation $validation);
}
