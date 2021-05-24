<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Sylius\RefundPlugin\Event\UnitsRefunded;

interface UnitsRefundedProcessManagerInterface
{
    public function __invoke(UnitsRefunded $event): void;
}
