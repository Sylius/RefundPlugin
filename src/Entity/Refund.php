<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Model\RefundType;

class Refund implements RefundInterface
{
    /** @var int|null */
    protected $id;

    /** @var OrderInterface */
    protected $order;

    /** @var int */
    protected $amount;

    /** @var int */
    protected $refundedUnitId;

    /** @var RefundType */
    protected $type;

    public function __construct(OrderInterface $order, int $amount, int $refundedUnitId, RefundType $type)
    {
        $this->order = $order;
        $this->amount = $amount;
        $this->refundedUnitId = $refundedUnitId;
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getOrderNumber(): string
    {
        return $this->order->getNumber();
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
