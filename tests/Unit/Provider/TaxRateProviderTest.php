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

namespace Tests\Sylius\RefundPlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\RefundPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\RefundPlugin\Provider\TaxRateProvider;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class TaxRateProviderTest extends TestCase
{
    private TaxRateProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new TaxRateProvider();
    }

    /** @test */
    function it_is_initializable(): void
    {
        self::assertInstanceOf(TaxRateProvider::class, $this->provider);
    }

    /** @test */
    function it_implements_tax_rate_provider_interface(): void
    {
        self::assertInstanceOf(TaxRateProviderInterface::class, $this->provider);
    }

    /** @test */
    function it_provides_a_tax_rate_from_tax_adjustment_details(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderItemUnit
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment
            ->expects(self::once())
            ->method('getDetails')
            ->willReturn(['taxRateAmount' => 0.2]);

        $result = $this->provider->provide($orderItemUnit);

        self::assertSame('20%', $result);
    }

    /** @test */
    function it_returns_null_if_there_is_no_tax_adjustment(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);

        $orderItemUnit
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]));

        $result = $this->provider->provide($orderItemUnit);

        self::assertNull($result);
    }

    /** @test */
    function it_throws_an_exception_if_there_is_no_tax_rate_amount_in_details_of_adjustment(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderItemUnit
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment
            ->expects(self::once())
            ->method('getDetails')
            ->willReturn([]);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->provide($orderItemUnit);
    }

    /** @test */
    function it_throws_an_exception_if_order_item_unit_has_more_adjustments_than_one(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $firstTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $secondTaxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderItemUnit
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment, $secondTaxAdjustment]));

        $this->expectException(MoreThanOneTaxAdjustment::class);

        $this->provider->provide($orderItemUnit);
    }
}
