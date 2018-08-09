<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

interface CreditMemoCustomerRelationCheckerInterface
{
    public function check(string $creditMemoId): void;
}
