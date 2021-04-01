<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Validation\Validation;

/**
 * @template A
 */
interface Reporter
{
    /**
     * @param Validation<mixed> $validation
     *
     * @return A
     */
    public function report(Validation $validation);
}
