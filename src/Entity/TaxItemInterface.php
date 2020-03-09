<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface TaxItemInterface extends ResourceInterface
{
    public function id(): ?int;

    public function label(): string;

    public function amount(): int;
}
