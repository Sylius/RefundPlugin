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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactory;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;

final class ShopBillingDataFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $shopBillingDataFactory;

    private ShopBillingDataFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopBillingDataFactory = $this->createMock(FactoryInterface::class);
        $this->factory = new ShopBillingDataFactory($this->shopBillingDataFactory);
    }

    #[Test]
    public function it_implements_shop_billing_data_factory_interface(): void
    {
        self::assertInstanceOf(ShopBillingDataFactoryInterface::class, $this->factory);
    }

    #[Test]
    public function it_creates_new_shop_billing_data(): void
    {
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);

        $this->shopBillingDataFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($shopBillingData);

        $result = $this->factory->createNew();

        self::assertSame($shopBillingData, $result);
    }

    #[Test]
    public function it_creates_new_shop_billing_data_with_data(): void
    {
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);

        $this->shopBillingDataFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($shopBillingData);

        $shopBillingData->expects(self::once())
            ->method('setCompany')
            ->with('Needful Things');

        $shopBillingData->expects(self::once())
            ->method('setTaxId')
            ->with('000222');

        $shopBillingData->expects(self::once())
            ->method('setCountryCode')
            ->with('US');

        $shopBillingData->expects(self::once())
            ->method('setStreet')
            ->with('Main St. 123');

        $shopBillingData->expects(self::once())
            ->method('setCity')
            ->with('Los Angeles');

        $shopBillingData->expects(self::once())
            ->method('setPostcode')
            ->with('90001');

        $result = $this->factory->createWithData('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001');

        self::assertSame($shopBillingData, $result);
    }
}
