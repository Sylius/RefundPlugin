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

namespace Tests\Sylius\RefundPlugin\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

final class ShopBillingDataTest extends TestCase
{
    private ShopBillingData $shopBillingData;

    protected function setUp(): void
    {
        $this->shopBillingData = new ShopBillingData();
    }

    /** @test */
    function it_implements_shop_billing_data_interface(): void
    {
        $this->assertInstanceOf(ShopBillingDataInterface::class, $this->shopBillingData);
    }

    /** @test */
    function it_has_no_id_by_default(): void
    {
        $this->assertNull($this->shopBillingData->getId());
    }

    /** @test */
    function it_has_a_company(): void
    {
        $this->shopBillingData->setCompany('Needful Things');
        $this->assertEquals('Needful Things', $this->shopBillingData->getCompany());
    }

    /** @test */
    function it_has_a_tax_id(): void
    {
        $this->shopBillingData->setTaxId('000222');
        $this->assertEquals('000222', $this->shopBillingData->getTaxId());
    }

    /** @test */
    function it_has_a_country_code(): void
    {
        $this->shopBillingData->setCountryCode('US');
        $this->assertEquals('US', $this->shopBillingData->getCountryCode());
    }

    /** @test */
    function it_has_a_street(): void
    {
        $this->shopBillingData->setStreet('Main St. 123');
        $this->assertEquals('Main St. 123', $this->shopBillingData->getStreet());
    }

    /** @test */
    function it_has_a_city(): void
    {
        $this->shopBillingData->setCity('Los Angeles');
        $this->assertEquals('Los Angeles', $this->shopBillingData->getCity());
    }

    /** @test */
    function it_has_a_postcode(): void
    {
        $this->shopBillingData->setPostcode('90001');
        $this->assertEquals('90001', $this->shopBillingData->getPostcode());
    }
}