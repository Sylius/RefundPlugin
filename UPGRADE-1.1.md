### UPGRADE FROM 1.0.X TO 1.1.0

1. The deprecated methods have been removed:

    - `Sylius\RefundPlugin\Entity\CustomerBillingData::id()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::firstName()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::lastName()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::fullName()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::street()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::postcode()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::countryCode()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::city()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::company()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::provinceName()`
    - `Sylius\RefundPlugin\Entity\CustomerBillingData::provinceCode()`
    - `Sylius\RefundPlugin\Entity\Refund::getOrderNumber()`
    - `Sylius\RefundPlugin\Entity\RefundPayment::getOrderNumber()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::id()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::company()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::taxId()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::countryCode()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::street()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::city()`
    - `Sylius\RefundPlugin\Entity\ShopBillingData::postcode()`
