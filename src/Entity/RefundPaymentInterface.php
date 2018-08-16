<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface RefundPaymentInterface
{
    public const STATE_NEW = 'New';
    public const STATE_PAID = 'Paid';

    public function getNumber(): string;

    public function getAmount(): int;

    public function getCurrencyCode(): string;

    public function getState(): string;
}
