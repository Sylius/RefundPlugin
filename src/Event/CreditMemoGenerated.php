<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class CreditMemoGenerated
{
    /** @var string */
    private $orderNumber;

    public function __construct(string $orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }
}
