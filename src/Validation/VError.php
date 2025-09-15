<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Validation;

final class VError
{
    /** @var mixed */
    private $value;
    private \Facile\PhpCodec\Validation\Context $context;
    private ?string $message = null;

    /**
     * @param mixed $value
     *
     * @psalm-param mixed       $value
     * @psalm-param Context     $context
     * @psalm-param string|null $message
     */
    public function __construct(
        $value,
        Context $context,
        ?string $message = null
    ) {
        $this->value = $value;
        $this->context = $context;
        $this->message = $message;
    }

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
