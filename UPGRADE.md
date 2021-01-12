### UPGRADE FROM 1.0.0-RC.5 TO 1.0.0-RC.6

1. The `Version20201208105207.php` migration was added which extends existing adjustments with additional details (context). Depending on the type of adjustment, additionally defined information are:
 * Taxation details (percentage and relation to tax rate)
 * Shipping details (shipping relation)
 * Taxation for shipping (combined details of percentage and shipping relation)

 This data is fetched based on two assumptions:
 * Order level taxes relates to shipping only (default Sylius behaviour)
 * Tax rate name has not change since the time, the first order has been placed

 If these are not true, please adjust migration accordingly to your need. To exclude following migration from execution run following code: 
    ```
    bin/console doctrine:migrations:version 'Sylius\RefundPlugin\Migrations\Version20201208105207' --add
    ```

### UPGRADE FROM 1.0.0-RC.3 TO 1.0.0-RC.4

1. Upgrade your application to [Sylius 1.8](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.8.md).

1. Remove previously copied migration files (You may check names of migrations to remove [here](https://github.com/Sylius/RefundPlugin/pull/205/commits/82e09b5bd8fa179da79870c9cb838d7ca289737a)).

### UPGRADE FROM 1.0.0-RC.2 TO 1.0.0-RC.3

1. `Sylius\RefundPlugin\Entity\RefundPaymentInterface` state constants values were changed to lowercase. Backward compatibility provided by migration.

1. Adjust new templates from the plugin (ref. [PR #198](https://github.com/Sylius/RefundPlugin/pull/198)) 

   > :warning: Check in your git repository, what has changed and only adjust templates where needed

    ```bash
    cp -R vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
    ```

1. Copy new migrations

    ```bash
    cp -R vendor/sylius/refund-plugin/migrations/{Version20200306145439.php,Version20200306153205.php,Version20200310094633.php,Version20200310185620.php} src/Migrations
    ```
   
1. Change usage of `Sylius\RefundPlugin\Entity\SequenceInterface` to `Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface`

1. Change usage of `Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface` to `Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface`

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
