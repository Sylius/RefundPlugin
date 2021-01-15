<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\RefundPlugin\Model\RefundType;

class Refund implements RefundInterface
{
    /** @var int|null */
    protected $id;

    /** @var string */
    protected $orderNumber;

    /** @var int */
    protected $amount;

    /** @var int */
    protected $refundedUnitId;

    /** @var RefundType */
    protected $type;

    public function __construct(string $orderNumber, int $amount, int $refundedUnitId, RefundType $type)
    {
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->refundedUnitId = $refundedUnitId;
        $this->type = $type;
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

    public function getType(): RefundType
    {
        return $this->type;
    }
}
