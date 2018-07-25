<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Entity\SequenceInterface;

final class CreditMemoSequenceFactory implements SequenceFactoryInterface
{
    public function createNew(): SequenceInterface
    {
        return new CreditMemoSequence();
    }
}
