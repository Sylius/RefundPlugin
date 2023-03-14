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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;

final class CustomerBillingDataFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $customerBillingDataFactory): void
    {
        $this->beConstructedWith($customerBillingDataFactory);
    }

    function it_implements_customer_billing_data_factory_interface(): void
    {
        $this->shouldImplement(CustomerBillingDataFactoryInterface::class);
    }

    function it_creates_a_new_customer_billing_data(
        FactoryInterface $customerBillingDataFactory,
        CustomerBillingDataInterface $billingData,
    ): void {
        $customerBillingDataFactory->createNew()->willReturn($billingData);

        $this->createNew()->shouldReturn($billingData);
    }

    function it_creates_a_new_customer_billing_data_with_data(
        CustomerBillingDataInterface $customerBillingData,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
    ): void {
        $customerBillingDataFactory->createNew()->willReturn($customerBillingData);

        $customerBillingData->setFirstName('Pablo')->shouldBeCalled();
        $customerBillingData->setLastName('Escobar')->shouldBeCalled();
        $customerBillingData->setStreet('Coke street')->shouldBeCalled();
        $customerBillingData->setPostcode('90-210')->shouldBeCalled();
        $customerBillingData->setCountryCode('CO')->shouldBeCalled();
        $customerBillingData->setCity('Bogota')->shouldBeCalled();
        $customerBillingData->setCompany('Coca cola but better')->shouldBeCalled();
        $customerBillingData->setProvinceName('Bogota')->shouldBeCalled();
        $customerBillingData->setProvinceCode('CO-DC')->shouldBeCalled();

        $this
            ->createWithData('Pablo', 'Escobar', 'Coke street', '90-210', 'CO', 'Bogota', 'Coca cola but better', 'Bogota', 'CO-DC')
            ->shouldBeLike($customerBillingData)
        ;
    }

    function it_creates_a_new_customer_billing_data_with_address(
        CustomerBillingDataInterface $customerBillingData,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        AddressInterface $address,
    ): void {
        $address->getFirstName()->willReturn('Pablo');
        $address->getLastName()->willReturn('Escobar');
        $address->getStreet()->willReturn('Coke street');
        $address->getPostcode()->willReturn('90-210');
        $address->getCountryCode()->willReturn('CO');
        $address->getCity()->willReturn('Bogota');
        $address->getCompany()->willReturn('Coca cola but better');
        $address->getProvinceName()->willReturn('Bogota');
        $address->getProvinceCode()->willReturn('CO-DC');

        $customerBillingDataFactory->createNew()->willReturn($customerBillingData);

        $customerBillingData->setFirstName('Pablo')->shouldBeCalled();
        $customerBillingData->setLastName('Escobar')->shouldBeCalled();
        $customerBillingData->setStreet('Coke street')->shouldBeCalled();
        $customerBillingData->setPostcode('90-210')->shouldBeCalled();
        $customerBillingData->setCountryCode('CO')->shouldBeCalled();
        $customerBillingData->setCity('Bogota')->shouldBeCalled();
        $customerBillingData->setCompany('Coca cola but better')->shouldBeCalled();
        $customerBillingData->setProvinceName('Bogota')->shouldBeCalled();
        $customerBillingData->setProvinceCode('CO-DC')->shouldBeCalled();

        $this
            ->createWithAddress($address)
            ->shouldReturn($customerBillingData)
        ;
    }
}
