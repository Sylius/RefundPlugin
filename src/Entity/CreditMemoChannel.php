<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoChannel
{
    /** @var string */
    private $code;

    /** @var string */
    private $name;

    /** @var string */
    private $color;

    public function __construct(string $code, string $name, string $color)
    {
        $this->code = $code;
        $this->name = $name;
        $this->color= $color;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function color(): string
    {
        return $this->color;
    }
}
