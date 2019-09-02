<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopBillingDataInterface as ChannelShopBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;

final class ShopBillingDataFactorySpec extends ObjectBehavior
{
    function it_implements_sequence_factory_interface(): void
    {
        $this->shouldImplement(ShopBillingDataFactoryInterface::class);
    }

    function it_creates_new_shop_billing_data_for_shop_channel_billing_data(
        ChannelShopBillingData $channelShopBillingData
    ): void {
        $channelShopBillingData->getCompany()->willReturn('Testfirma');
        $channelShopBillingData->getTaxId()->willReturn('UA-123456');
        $channelShopBillingData->getCountryCode()->willReturn('DE');
        $channelShopBillingData->getStreet()->willReturn('Teststraße 17');
        $channelShopBillingData->getCity()->willReturn('Teststadt');
        $channelShopBillingData->getPostcode()->willReturn('12345');

        $this->createForChannelShopBillingData($channelShopBillingData)->shouldBeLike(
            new ShopBillingData(
                'Testfirma',
                'UA-123456',
                'DE',
                'Teststraße 17',
                'Teststadt',
                '12345'
            )
        );
    }

    function it_creates_new_shop_billing_data_for_shop_channel_billing_data_no_street_and_company(
        ChannelShopBillingData $channelShopBillingData
    ): void {
        $channelShopBillingData->getTaxId()->willReturn('UA-123456');
        $channelShopBillingData->getCountryCode()->willReturn('DE');
        $channelShopBillingData->getCity()->willReturn('Teststadt');
        $channelShopBillingData->getPostcode()->willReturn('12345');

        $channelShopBillingData->getCompany()->willReturn(null);
        $channelShopBillingData->getStreet()->willReturn(null);

        $this->createForChannelShopBillingData($channelShopBillingData)->shouldBeNull();
    }

    function it_creates_new_shop_billing_data_for_shop_channel_billing_data_null(): void
    {
        $this->createForChannelShopBillingData(null)->shouldBeNull();
    }
}
