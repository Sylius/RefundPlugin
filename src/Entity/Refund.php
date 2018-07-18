<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class Refund implements RefundInterface
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $orderNumber;

    /** @var int|null */
    private $amount;

    /** @var int|null */
    private $refundedUnitId;

    public function __construct(string $orderNumber, int $amount, int $refundedUnitId)
    {
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->refundedUnitId = $refundedUnitId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getRefundedUnitId(): int
    {
        return $this->refundedUnitId;
    }
}
