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
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;

final class CustomerBillingDataTest extends TestCase
{
    private CustomerBillingData $customerBillingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerBillingData = new CustomerBillingData();
    }

    /** @test */
    public function it_implements_customer_billing_data_interface(): void
    {
        self::assertInstanceOf(CustomerBillingDataInterface::class, $this->customerBillingData);
    }

    /** @test */
    public function it_has_no_id_by_default(): void
    {
        self::assertNull($this->customerBillingData->getId());
    }

    /** @test */
    public function it_has_an_id(): void
    {
        $this->customerBillingData->setId(1234);
        self::assertEquals(1234, $this->customerBillingData->getId());
    }

    /** @test */
    public function it_has_a_first_name(): void
    {
        $this->customerBillingData->setFirstName('Rick');
        self::assertEquals('Rick', $this->customerBillingData->getFirstName());
    }

    /** @test */
    public function it_has_a_last_name(): void
    {
        $this->customerBillingData->setLastName('Sanchez');
        self::assertEquals('Sanchez', $this->customerBillingData->getLastName());
    }

    /** @test */
    public function it_has_a_full_name(): void
    {
        $this->customerBillingData->setFirstName('Rick');
        $this->customerBillingData->setLastName('Sanchez');
        self::assertEquals('Rick Sanchez', $this->customerBillingData->getFullName());
    }

    /** @test */
    public function it_has_a_company(): void
    {
        $this->customerBillingData->setCompany('Curse Purge Plus!');
        self::assertEquals('Curse Purge Plus!', $this->customerBillingData->getCompany());
    }

    /** @test */
    public function it_has_a_street(): void
    {
        $this->customerBillingData->setStreet('Main St. 3322');
        self::assertEquals('Main St. 3322', $this->customerBillingData->getStreet());
    }

    /** @test */
    public function it_has_a_postcode(): void
    {
        $this->customerBillingData->setPostcode('90802');
        self::assertEquals('90802', $this->customerBillingData->getPostcode());
    }

    /** @test */
    public function it_has_a_country_code(): void
    {
        $this->customerBillingData->setCountryCode('US');
        self::assertEquals('US', $this->customerBillingData->getCountryCode());
    }

    /** @test */
    public function it_has_a_city(): void
    {
        $this->customerBillingData->setCity('Los Angeles');
        self::assertEquals('Los Angeles', $this->customerBillingData->getCity());
    }

    /** @test */
    public function it_has_a_province_name(): void
    {
        $this->customerBillingData->setProvinceName('Baldwin Hills');
        self::assertEquals('Baldwin Hills', $this->customerBillingData->getProvinceName());
    }

    /** @test */
    public function it_has_a_province_code(): void
    {
        $this->customerBillingData->setProvinceCode('323');
        self::assertEquals('323', $this->customerBillingData->getProvinceCode());
    }
}
