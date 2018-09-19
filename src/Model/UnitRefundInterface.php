<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

interface UnitRefundInterface
{
    public function id(): int;
    public function total(): int;
}
