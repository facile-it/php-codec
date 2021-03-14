<?php declare(strict_types=1);

namespace Pybatt\Codec;

use Pybatt\Codec\Validation\Validation;

/**
 * @template A
 */
interface Reporter
{
    public const VALUE_UNDEFINED = 'd5d4cd07616a542891b7ec2d0257b3a24b69856e';

    /**
     * @param Validation<mixed> $validation
     * @return A
     */
    public function report(Validation $validation);
}
