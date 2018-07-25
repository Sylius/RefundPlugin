<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

interface NumberGenerator
{
    public function generate(): string;
}
