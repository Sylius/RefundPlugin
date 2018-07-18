<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface RefundInterface extends ResourceInterface
{
    public function getOrderNumber(): string;

    public function getAmount(): int;

    public function getRefundedUnitId(): int;
}
