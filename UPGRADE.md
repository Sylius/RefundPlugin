### UPGRADE FROM 1.0.0-RC.10 TO 1.0.0-RC.11

1. `orderNumber` field on `Sylius\RefundPlugin\Entity\Refund` and `Sylius\RefundPlugin\Entity\RefundPayment` has been removed 
    and replaced with relation to `Order` entity.

1. `Sylius\RefundPlugin\Entity\RefundInterface::getOrderNumber` function is left due to easier and smoother upgrades,
   but is also deprecated and will be removed in the `v1.0.0` release. Use `Sylius\RefundPlugin\Entity\RefundInterface::getOrder` instead.

1. `Sylius\RefundPlugin\Entity\RefundPaymentInterface::getOrderNumber` function is left due to easier and smoother upgrades,
   but is also deprecated and will be removed in the `v1.0.0` release. Use `Sylius\RefundPlugin\Entity\RefundPaymentInterface::getOrder` instead.

1. `Sylius\RefundPlugin\Creator\RefundCreator` takes `Sylius\Component\Core\Repository\OrderRepositoryInterface`
   as the 3rd argument.

1. `Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager` takes `Sylius\Component\Core\Repository\OrderRepositoryInterface`
   as the 4th argument.

1. `Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface::invoke` takes `OrderInterface $order` as an argument
   instead of `string $orderNumber`

1. `Sylius\RefundPlugin\Factory\RefundPaymentFactory::createWithData` takes `OrderInterface $order` as an argument
   instead of `string $orderNumber`

### UPGRADE FROM 1.0.0-RC.9 TO 1.0.0-RC.10

1. Support for Sylius 1.8 has been dropped, upgrade your application to [Sylius 1.9](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.9.md) 
or [Sylius 1.10](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.10.md). 

1. Remove usage of:
    * `Sylius\RefundPlugin\Entity\AdjustmentInterface`
    * `Sylius\RefundPlugin\Entity\AdjustmentTrait`
    * `Sylius\RefundPlugin\Entity\ShipmentInterface`
    * `Sylius\RefundPlugin\Entity\ShipmentTrait`

1. Delete removed migrations from the migrations table by running:  
    ```
    bin/console doctrine:migrations:version Sylius\\RefundPlugin\\Migrations\\Version20201208105207 --delete
    bin/console doctrine:migrations:version Sylius\\RefundPlugin\\Migrations\\Version20201130071338 --delete
    bin/console doctrine:migrations:version Sylius\\RefundPlugin\\Migrations\\Version20201204071301 --delete
    ```

1. Command bus `sylius_refund_plugin.command_bus` has been replaced with `sylius.command_bus`.

1. Event bus `sylius_refund_plugin.event_bus` has been replaced with `sylius.event_bus`.

1. `Sylius\RefundPlugin\Grid\Filter\ChannelFilter` and `Sylius\RefundPlugin\Form\Type\ChannelFilterType` services 
have been removed and channel filter configuration in grid has been replaced by entity filter.

1. Constructor of `Sylius\RefundPlugin\Entity\CreditMemo` has been removed and now `CreditMemo` entity 
is created by `Sylius\RefundPlugin\Factory\CreditMemoFactory`.

1. The constructor of `Sylius\RefundPlugin\Generator\CreditMemoGenerator` has been changed:

    ```diff
        public function __construct(
            LineItemsConverterInterface $lineItemsConverter,
            LineItemsConverterInterface $shipmentLineItemsConverter,
            TaxItemsGeneratorInterface $taxItemsGenerator,
    -       NumberGenerator $creditMemoNumberGenerator,
    -       CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider,
    -       CreditMemoIdentifierGeneratorInterface $uuidCreditMemoIdentifierGenerator
    +       CreditMemoFactoryInterface $creditMemoFactory
        ) {
            ...
        }
    ```

1. Post-units refunded process (containing credit memo and refund payment generation) was changed to synchronous step. Refund payment is therefore
always generated after credit memo. Technical changes:
    * `Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager` and `Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager` no longer 
       directly listen to `Sylius\RefundPlugin\Event\UnitsRefunded` event. `Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManager` uses them to 
       facilitate post-units refunding process.
    * Their `__invoke` methods were replaced by `Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface::next(UnitsRefunded $unitsRefunded)`. 

1. `Sylius\RefundPlugin\Generator\NumberGenerator` has been changed to `Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface`
and its method has been changed from `public function generate(): string` to `public function generate(OrderInterface $order, \DateTimeInterface $issuedAt): string`.

1. Service name and definition has been changed from `Sylius\RefundPlugin\Generator\SequentialNumberGenerator` to `Sylius\RefundPlugin\Generator\SequentialCreditMemoNumberGenerator`
and its last argument `CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider` has been removed from constructor.

### UPGRADE FROM 1.0.0-RC.7 TO 1.0.0-RC.8

1. The `fully_refunded` state and the `refund` transition have been removed from `sylius_order` state machine.

1. `Sylius\RefundPlugin\Provider\LabelBasedTaxRateProvider` has been changed to `Sylius\RefundPlugin\Provider\TaxRateProvider`.

1. The method `Sylius\RefundPlugin\Provider\TaxRateProviderInterface` has been changed 
from `provide(OrderItemUnitInterface $orderItemUnit): ?string` to `provide(AdjustableInterface $adjustable): ?string`.

1. The `TaxRateProviderInterface $taxRateProvider` has been added as the second argument in constructor of `Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter`

1. Service definition for `Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter` has been changed from 
`Sylius\RefundPlugin\Converter\ShipmentLineItemsConverterInterface` to `Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter`

1. `Sylius\RefundPlugin\Converter\LineItemsConverter` has been changed to `Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter`
and its service definition has been changed from `Sylius\RefundPlugin\Converter\LineItemsConverterInterface` to `Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter`

1. The suffix `Exception` has been removed from classes: 
    * `Sylius\RefundPlugin\Exception\InvalidRefundAmountException` 
    * `Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException`
    * `Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException`

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

1. Add traits that enhance Adjustment and Shipment models from Sylius. These traits are not covered by 
the backward compatibility promise and it will be removed after update Sylius to 1.9. It is a duplication of a logic 
from Sylius to provide proper adjustments handling.

```php
<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\AdjustmentTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_adjustment")
 */
class Adjustment extends BaseAdjustment implements AdjustmentInterface
{
    use AdjustmentTrait;
}
```

```php
<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\Shipment as BaseShipment;
use Sylius\RefundPlugin\Entity\ShipmentInterface;
use Sylius\RefundPlugin\Entity\ShipmentTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_shipment")
 */
class Shipment extends BaseShipment implements ShipmentInterface
{
    use ShipmentTrait;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, BaseAdjustmentInterface> $this->adjustments */
        $this->adjustments = new ArrayCollection();
    }
}

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
