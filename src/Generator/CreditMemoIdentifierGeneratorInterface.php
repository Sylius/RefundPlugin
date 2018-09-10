<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

interface CreditMemoIdentifierGeneratorInterface
{
    public function generate(): string;
}
