<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

final class CurrentDateTimeImmutableProvider implements CurrentDateTimeImmutableProviderInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
