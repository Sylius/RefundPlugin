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

class ShopBillingDataFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $shopBillingDataFactory): void
    {
        $this->beConstructedWith($shopBillingDataFactory);
    }

    public function it_implements_shop_billing_data_factory_interface(): void
    {
        $this->shouldImplement(ShopBillingDataFactoryInterface::class);
    }

    public function it_creates_new_shop_billing_data(
        FactoryInterface $shopBillingDataFactory,
        ShopBillingDataInterface $shopBillingData
    ): void {
        $shopBillingDataFactory->createNew()->willReturn($shopBillingData);

        $this->createNew()->shouldReturn($shopBillingData);
    }

    public function it_creates_new_shop_billing_data_with_data(
        ShopBillingDataInterface $shopBillingData,
        FactoryInterface $shopBillingDataFactory
    ): void {
        $shopBillingDataFactory->createNew()->willReturn($shopBillingData);

        $shopBillingData->setCompany('Needful Things')->shouldBeCalled();
        $shopBillingData->setTaxId('000222')->shouldBeCalled();
        $shopBillingData->setCountryCode('US')->shouldBeCalled();
        $shopBillingData->setStreet('Main St. 123')->shouldBeCalled();
        $shopBillingData->setCity('Los Angeles')->shouldBeCalled();
        $shopBillingData->setPostcode('90001')->shouldBeCalled();

        $this
            ->createWithData('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001')
            ->shouldBeLike($shopBillingData)
        ;
    }
}
