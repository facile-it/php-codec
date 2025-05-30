<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Validation\Validation;

/**
 * @template T
 */
interface Reporter
{
    /**
     * @template A
     *
     * @param Validation<A> $validation
     *
     * @return T
     */
    public function report(Validation $validation);
}
