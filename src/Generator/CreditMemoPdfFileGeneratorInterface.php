<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Model\CreditMemoPdf;

interface CreditMemoPdfFileGeneratorInterface
{
    public function generate(int $creditMemoId): CreditMemoPdf;
}
