<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Sender;

use Sylius\RefundPlugin\Entity\CreditMemoInterface;

interface CreditMemoEmailSenderInterface
{
    public function send(CreditMemoInterface $creditMemo, string $recipient): void;
}
