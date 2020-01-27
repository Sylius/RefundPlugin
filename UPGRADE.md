### UPGRADE FROM 1.0.0-RC.1 TO 1.0.0-RC.2

1. `CreditMemoUnit` changed to `LineItem` which is a resource entity now

2. `CreditMemoUnitGeneratorInterface` changed to `LineItemsConverterInterface` and moved from `Generator` to `Converter` directory

3. `OrderItemUnitCreditMemoUnitGenerator` changed to `LineItemsConverter` and moved from `Generator` to `Converter` directory

4. `ShipmentCreditMemoUnitGenerator` changed to `ShipmentLineItemsConverter` and moved from `Generator` to `Converter` directory

There are no migrations that provide backward compatibility, save current credit memos before upgrading the version of plugin. 

### UPGRADE FROM 0.10.1 TO 1.0.0-RC.1

1. `OfflineRefundPaymentMethodsProvider` renamed to `SupportedRefundPaymentMethodsProvider` with the supported gateways array as the 2nd argument
(by default only `offline` gateway is passed and therefore supported).

### UPGRADE FROM 0.8.0 TO 0.9.0

1. Removed ``CreditMemoChannel`` and replaced by ``Sylius\Component\Core\Model\ChannelInterface``.

2. Replaced  ``CustomerBillingData`` and ``ShopBillingData`` value objects by entities with ``CustomerBillingDataInterface`` and ``ShopBillingDataInterface`` interfaces.
