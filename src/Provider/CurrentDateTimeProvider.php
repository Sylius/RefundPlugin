<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

final class CurrentDateTimeProvider implements CurrentDateTimeProviderInterface
{
    public function now(): \DateTimeInterface
    {
        return new \DateTime();
    }
}
