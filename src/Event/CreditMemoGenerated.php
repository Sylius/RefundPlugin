<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class CreditMemoGenerated
{
    /** @var string */
    private $number;

    /** @var string */
    private $orderNumber;

    public function __construct(string $number, string $orderNumber)
    {
        $this->number = $number;
        $this->orderNumber = $orderNumber;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }
}
