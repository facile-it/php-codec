<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

final class VError
{
    /**
     * @psalm-param mixed       $value
     * @psalm-param Context     $context
     * @psalm-param string|null $message
     *
     * @param mixed $value
     */
    public function __construct(private $value, private readonly \Facile\PhpCodec\Validation\Context $context, private readonly ?string $message = null) {}

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
