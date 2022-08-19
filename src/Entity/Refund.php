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

use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

class Refund implements RefundInterface
{
    protected ?int $id = null;

    protected OrderInterface $order;

    protected int $amount;

    protected OrderItemUnitInterface $orderItemUnit;

    protected RefundTypeInterface $type;

    public function __construct(OrderInterface $order, int $amount, OrderItemUnitInterface $orderItemUnit, RefundTypeInterface $type)
    {
        $this->order = $order;
        $this->amount = $amount;
        $this->orderItemUnit = $orderItemUnit;
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

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use Refund::getOrder() instead */
    public function getOrderNumber(): string
    {
        return (string) $this->order->getNumber();
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getOrderItemUnit(): OrderItemUnitInterface
    {
        return $this->orderItemUnit;
    }

    public function getType(): RefundTypeInterface
    {
        return $this->type;
    }
}
