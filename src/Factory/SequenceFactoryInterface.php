<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\SequenceInterface;

interface SequenceFactoryInterface
{
    public function createNew(): SequenceInterface;
}
