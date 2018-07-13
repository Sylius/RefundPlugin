<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Order;

use Sylius\Behat\Page\Admin\Order\ShowPageInterface as BaseOrderShowPageInterface;

interface ShowPageInterface extends BaseOrderShowPageInterface
{
    public function hasRefundsButton(): bool;
}
