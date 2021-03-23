<?php declare(strict_types=1);

namespace Facile\Codec;

use Facile\Codec\Validation\Validation;

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
