<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\ShopBillingDataInterface as ChannelShopBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;

interface ShopBillingDataFactoryInterface
{
    public function createForChannelShopBillingData(?ChannelShopBillingData $channelShopBillingData): ?ShopBillingData;
}
