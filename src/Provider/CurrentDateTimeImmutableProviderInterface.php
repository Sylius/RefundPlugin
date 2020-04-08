<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface CurrentDateTimeImmutableProviderInterface
{
    public function now(): \DateTimeImmutable;
}
