<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactory;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;

final class ShopBillingDataFactoryTest extends TestCase
{
    private FactoryInterface $shopBillingDataFactory;
    private ShopBillingDataFactory $factory;

    protected function setUp(): void
    {
        $this->shopBillingDataFactory = $this->createMock(FactoryInterface::class);
        $this->factory = new ShopBillingDataFactory($this->shopBillingDataFactory);
    }

    public function testItImplementsShopBillingDataFactoryInterface(): void
    {
        $this->assertInstanceOf(ShopBillingDataFactoryInterface::class, $this->factory);
    }

    public function testItCreatesNewShopBillingData(): void
    {
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);

        $this->shopBillingDataFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($shopBillingData);

        $result = $this->factory->createNew();

        $this->assertSame($shopBillingData, $result);
    }

    public function testItCreatesNewShopBillingDataWithData(): void
    {
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);

        $this->shopBillingDataFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($shopBillingData);

        $shopBillingData->expects($this->once())
            ->method('setCompany')
            ->with('Needful Things');

        $shopBillingData->expects($this->once())
            ->method('setTaxId')
            ->with('000222');

        $shopBillingData->expects($this->once())
            ->method('setCountryCode')
            ->with('US');

        $shopBillingData->expects($this->once())
            ->method('setStreet')
            ->with('Main St. 123');

        $shopBillingData->expects($this->once())
            ->method('setCity')
            ->with('Los Angeles');

        $shopBillingData->expects($this->once())
            ->method('setPostcode')
            ->with('90001');

        $result = $this->factory->createWithData('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001');

        $this->assertSame($shopBillingData, $result);
    }
}