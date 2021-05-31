<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

interface NumberGeneratorInterface
{
    public function generate(): string;
}
