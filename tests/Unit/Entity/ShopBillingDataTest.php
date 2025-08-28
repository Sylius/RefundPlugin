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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

final class ShopBillingDataTest extends TestCase
{
    private ShopBillingData $shopBillingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopBillingData = new ShopBillingData();
    }

    #[Test]
    public function it_implements_shop_billing_data_interface(): void
    {
        self::assertInstanceOf(ShopBillingDataInterface::class, $this->shopBillingData);
    }

    #[Test]
    public function it_has_no_id_by_default(): void
    {
        self::assertNull($this->shopBillingData->getId());
    }

    #[Test]
    public function it_has_a_company(): void
    {
        $this->shopBillingData->setCompany('Needful Things');
        self::assertEquals('Needful Things', $this->shopBillingData->getCompany());
    }

    #[Test]
    public function it_has_a_tax_id(): void
    {
        $this->shopBillingData->setTaxId('000222');
        self::assertEquals('000222', $this->shopBillingData->getTaxId());
    }

    #[Test]
    public function it_has_a_country_code(): void
    {
        $this->shopBillingData->setCountryCode('US');
        self::assertEquals('US', $this->shopBillingData->getCountryCode());
    }

    #[Test]
    public function it_has_a_street(): void
    {
        $this->shopBillingData->setStreet('Main St. 123');
        self::assertEquals('Main St. 123', $this->shopBillingData->getStreet());
    }

    #[Test]
    public function it_has_a_city(): void
    {
        $this->shopBillingData->setCity('Los Angeles');
        self::assertEquals('Los Angeles', $this->shopBillingData->getCity());
    }

    #[Test]
    public function it_has_a_postcode(): void
    {
        $this->shopBillingData->setPostcode('90001');
        self::assertEquals('90001', $this->shopBillingData->getPostcode());
    }
}
