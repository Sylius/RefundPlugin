<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;

final class CreditMemoSequenceFactory implements CreditMemoSequenceFactoryInterface
{
    public function createNew(): CreditMemoSequenceInterface
    {
        return new CreditMemoSequence();
    }
}
