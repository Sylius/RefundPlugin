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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactory;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;

final class CustomerBillingDataFactoryTest extends TestCase
{
    private FactoryInterface $customerBillingDataFactory;
    private CustomerBillingDataFactory $factory;

    protected function setUp(): void
    {
        $this->customerBillingDataFactory = $this->createMock(FactoryInterface::class);
        $this->factory = new CustomerBillingDataFactory($this->customerBillingDataFactory);
    }

    public function testItImplementsCustomerBillingDataFactoryInterface(): void
    {
        $this->assertInstanceOf(CustomerBillingDataFactoryInterface::class, $this->factory);
    }

    public function testItCreatesANewCustomerBillingData(): void
    {
        $billingData = $this->createMock(CustomerBillingDataInterface::class);

        $this->customerBillingDataFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($billingData);

        $result = $this->factory->createNew();

        $this->assertSame($billingData, $result);
    }

    public function testItCreatesANewCustomerBillingDataWithData(): void
    {
        $customerBillingData = $this->createMock(CustomerBillingDataInterface::class);

        $this->customerBillingDataFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($customerBillingData);

        $customerBillingData->expects($this->once())
            ->method('setFirstName')
            ->with('Pablo');

        $customerBillingData->expects($this->once())
            ->method('setLastName')
            ->with('Escobar');

        $customerBillingData->expects($this->once())
            ->method('setStreet')
            ->with('Coke street');

        $customerBillingData->expects($this->once())
            ->method('setPostcode')
            ->with('90-210');

        $customerBillingData->expects($this->once())
            ->method('setCountryCode')
            ->with('CO');

        $customerBillingData->expects($this->once())
            ->method('setCity')
            ->with('Bogota');

        $customerBillingData->expects($this->once())
            ->method('setCompany')
            ->with('Coca cola but better');

        $customerBillingData->expects($this->once())
            ->method('setProvinceName')
            ->with('Bogota');

        $customerBillingData->expects($this->once())
            ->method('setProvinceCode')
            ->with('CO-DC');

        $result = $this->factory->createWithData('Pablo', 'Escobar', 'Coke street', '90-210', 'CO', 'Bogota', 'Coca cola but better', 'Bogota', 'CO-DC');

        $this->assertSame($customerBillingData, $result);
    }

    public function testItCreatesANewCustomerBillingDataWithAddress(): void
    {
        $customerBillingData = $this->createMock(CustomerBillingDataInterface::class);
        $address = $this->createMock(AddressInterface::class);

        // These methods are called twice - once for assertions, once for createWithData parameters
        $address->expects($this->exactly(2))
            ->method('getFirstName')
            ->willReturn('Pablo');

        $address->expects($this->exactly(2))
            ->method('getLastName')
            ->willReturn('Escobar');

        $address->expects($this->exactly(2))
            ->method('getStreet')
            ->willReturn('Coke street');

        $address->expects($this->exactly(2))
            ->method('getPostcode')
            ->willReturn('90-210');

        $address->expects($this->exactly(2))
            ->method('getCountryCode')
            ->willReturn('CO');

        $address->expects($this->exactly(2))
            ->method('getCity')
            ->willReturn('Bogota');

        $address->expects($this->once())
            ->method('getCompany')
            ->willReturn('Coca cola but better');

        $address->expects($this->once())
            ->method('getProvinceName')
            ->willReturn('Bogota');

        $address->expects($this->once())
            ->method('getProvinceCode')
            ->willReturn('CO-DC');

        $this->customerBillingDataFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($customerBillingData);

        $customerBillingData->expects($this->once())
            ->method('setFirstName')
            ->with('Pablo');

        $customerBillingData->expects($this->once())
            ->method('setLastName')
            ->with('Escobar');

        $customerBillingData->expects($this->once())
            ->method('setStreet')
            ->with('Coke street');

        $customerBillingData->expects($this->once())
            ->method('setPostcode')
            ->with('90-210');

        $customerBillingData->expects($this->once())
            ->method('setCountryCode')
            ->with('CO');

        $customerBillingData->expects($this->once())
            ->method('setCity')
            ->with('Bogota');

        $customerBillingData->expects($this->once())
            ->method('setCompany')
            ->with('Coca cola but better');

        $customerBillingData->expects($this->once())
            ->method('setProvinceName')
            ->with('Bogota');

        $customerBillingData->expects($this->once())
            ->method('setProvinceCode')
            ->with('CO-DC');

        $result = $this->factory->createWithAddress($address);

        $this->assertSame($customerBillingData, $result);
    }
}