<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;

final class ShopBillingDataFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $shopBillingDataFactory): void
    {
        $this->beConstructedWith($shopBillingDataFactory);
    }

    function it_implements_shop_billing_data_factory_interface(): void
    {
        $this->shouldImplement(ShopBillingDataFactoryInterface::class);
    }

    function it_creates_new_shop_billing_data(
        FactoryInterface $shopBillingDataFactory,
        ShopBillingDataInterface $shopBillingData,
    ): void {
        $shopBillingDataFactory->createNew()->willReturn($shopBillingData);

        $this->createNew()->shouldReturn($shopBillingData);
    }

    function it_creates_new_shop_billing_data_with_data(
        ShopBillingDataInterface $shopBillingData,
        FactoryInterface $shopBillingDataFactory,
    ): void {
        $shopBillingDataFactory->createNew()->willReturn($shopBillingData);

        $shopBillingData->setCompany('Needful Things');
        $shopBillingData->setTaxId('000222');
        $shopBillingData->setCountryCode('US');
        $shopBillingData->setStreet('Main St. 123');
        $shopBillingData->setCity('Los Angeles');
        $shopBillingData->setPostcode('90001');

        $this
            ->createWithData('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001')
            ->shouldBeLike($shopBillingData)
        ;
    }
}
