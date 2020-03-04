### UPGRADE FROM 1.0.0-RC.2 TO 1.0.0-RC.3

1. `Sylius\RefundPlugin\Entity\RefundPaymentInterface` state constants values were changed to lowercase. Backward compatibility provided by migration.

### UPGRADE FROM 1.0.0-RC.1 TO 1.0.0-RC.2

1. `Sylius\RefundPlugin\Entity\CreditMemoUnit` was changed to `Sylius\RefundPlugin\Entity\LineItem` which is a resource entity now.

2. `Sylius\RefundPlugin\Generator\CreditMemoUnitGeneratorInterface` was changed to `Sylius\RefundPlugin\Converter\LineItemsConverterInterface`.

3. `Sylius\RefundPlugin\Generator\OrderItemUnitCreditMemoUnitGenerator` was changed to `Sylius\RefundPlugin\Converter\LineItemsConverter`.

4. `Sylius\RefundPlugin\Generator\ShipmentCreditMemoUnitGenerator` was changed to `Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter`.

5. `Sylius\RefundPlugin\Entity\TaxItem` became a resource entity.

There are no migrations that provide backward compatibility, save current credit memos before upgrading the version of plugin.

### UPGRADE FROM 0.10.1 TO 1.0.0-RC.1

1. `OfflineRefundPaymentMethodsProvider` renamed to `SupportedRefundPaymentMethodsProvider` with the supported gateways array as the 2nd argument
(by default only `offline` gateway is passed and therefore supported).

### UPGRADE FROM 0.8.0 TO 0.9.0

1. Removed ``CreditMemoChannel`` and replaced by ``Sylius\Component\Core\Model\ChannelInterface``.

2. Replaced  ``CustomerBillingData`` and ``ShopBillingData`` value objects by entities with ``CustomerBillingDataInterface`` and ``ShopBillingDataInterface`` interfaces.
