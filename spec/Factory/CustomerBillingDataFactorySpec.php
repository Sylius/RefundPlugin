<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;

final class CustomerBillingDataFactorySpec extends ObjectBehavior
{
    function it_implements_sequence_factory_interface(): void
    {
        $this->shouldImplement(CustomerBillingDataFactoryInterface::class);
    }

    function it_creates_new_customer_billing_data_for_order(
        OrderInterface $order,
        AddressInterface $address
    ): void {
        $order->getBillingAddress()->willReturn($address);
        $address->getFirstName()->willReturn('Max');
        $address->getLastName()->willReturn('Mustermann');
        $address->getStreet()->willReturn('Musterstraße 17');
        $address->getPostcode()->willReturn('12345');
        $address->getCountryCode()->willReturn('DE');
        $address->getCity()->willReturn('Musterstadt');
        $address->getCompany()->willReturn('Testfirma');
        $address->getProvinceName()->willReturn(null);
        $address->getProvinceCode()->willReturn(null);

        $this->createForOrder($order)->shouldBeLike(
            new CustomerBillingData(
                'Max Mustermann', 'Musterstraße 17', '12345', 'DE', 'Musterstadt', 'Testfirma', null, null
            )
        );
    }
}
