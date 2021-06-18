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
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;

class ShopBillingDataFactorySpec extends ObjectBehavior
{
    public function it_implements_shop_billing_data_factory_interface(): void
    {
        $this->shouldImplement(ShopBillingDataFactoryInterface::class);
    }

    public function it_doesnt_support_create_new_method(): void
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('createNew');
    }

    public function it_creates_new_shop_billing_data_with_data(): void
    {
        $shopBillingData = new ShopBillingData();

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
