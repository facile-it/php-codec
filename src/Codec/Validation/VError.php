<?php declare(strict_types=1);

namespace Pybatt\Codec\Validation;

class VError
{
    /** @var mixed */
    private $value;
    /** @var Context */
    private $context;
    /** @var string|null */
    private $message;

    /**
     * @param mixed $value
     * @param Context $context
     * @param string|null $message
     */
    public function __construct(
        $value,
        Context $context,
        ?string $message = null
    )
    {
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
