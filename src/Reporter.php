<?php

declare(strict_types=1);

namespace Facile\PhpCodec;

use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template T
 */
interface Reporter
{
    /**
     * @psalm-template A
     * @psalm-param Validation<A> $validation
     *
     * @return T
     */
    public function report(Validation $validation);
}
