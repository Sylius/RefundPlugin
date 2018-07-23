<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface CurrentDateTimeProviderInterface
{
    public function now(): \DateTimeInterface;
}
