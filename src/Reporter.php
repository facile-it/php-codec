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
     * @psalm-return T
     *
     * @return mixed
     */
    public function report(Validation $validation);
}
