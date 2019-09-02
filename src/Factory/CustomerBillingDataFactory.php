<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;

class CustomerBillingDataFactory implements CustomerBillingDataFactoryInterface
{
    public function createForOrder(OrderInterface $order): CustomerBillingDataInterface
    {
        /** @var AddressInterface $address */
        $address = $order->getBillingAddress();

        return new CustomerBillingData(
            $address->getFirstName() . ' ' . $address->getLastName(),
            $address->getStreet(),
            $address->getPostcode(),
            $address->getCountryCode(),
            $address->getCity(),
            $address->getCompany(),
            $address->getProvinceName(),
            $address->getProvinceCode()
        );
    }
}
