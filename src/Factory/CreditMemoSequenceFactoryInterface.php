<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;

interface CreditMemoSequenceFactoryInterface
{
    public function createNew(): CreditMemoSequenceInterface;
}
