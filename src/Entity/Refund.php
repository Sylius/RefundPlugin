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
use Sylius\RefundPlugin\Model\RefundTypeInterface;

class Refund implements RefundInterface
{
    protected ?int $id = null;

    protected OrderInterface $order;

    protected int $amount;

    protected int $refundedUnitId;

    protected RefundTypeInterface $type;

    public function __construct(OrderInterface $order, int $amount, int $refundedUnitId, RefundTypeInterface $type)
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

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getRefundedUnitId(): int
    {
        return $this->refundedUnitId;
    }

    public function getType(): RefundTypeInterface
    {
        return $this->type;
    }
}
