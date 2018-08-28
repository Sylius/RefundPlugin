<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class UnitRefund
{
    /** @var int */
    private $unitId;

    /** @var int */
    private $total;

    public function __construct(int $unitId, int $total)
    {
        $this->unitId = $unitId;
        $this->total = $total;
    }

    public function unitId(): int
    {
        return $this->unitId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
