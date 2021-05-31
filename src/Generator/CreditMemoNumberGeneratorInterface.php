<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderInterface;

interface CreditMemoNumberGeneratorInterface
{
    public function generate(OrderInterface $order, \DateTimeInterface $issuedAt): string;
}
