<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Sylius\RefundPlugin\Event\UnitsRefunded;

interface UnitsRefundedProcessStepInterface
{
    public function next(UnitsRefunded $unitsRefunded): void;
}
