### UPGRADE FROM 1.3.X TO 1.4.0

1. The `Sylius\RefundPlugin\Listener\ShipmentRefundedEventListener` has been removed in favor of
   `Sylius\RefundPlugin\Listener\UnitRefundedEventListener` and the `UnitRefundedEventListener` listens now to all
   events implementing `Sylius\RefundPlugin\Event\UnitRefundedInterface`.

#### Backward compatible changes

All changes below are backward compatible, but we recommend upgrading to 1.4 as in the next major version we remove
deprecated code:

1. The constructor of the `Sylius\RefundPlugin\Command\GenerateCreditMemo` command has been changed:

   ```diff
       public function __construct(
           private string $orderNumber,
           private int $total,
   -       /** @var array|OrderItemUnitRefund[] */
   +       /** @var array|UnitRefundInterface[] */
           private array $units,
   -       /** @var array|ShipmentRefund[] */
   -       private array $shipments,      
           private string $comment,
       ) {
           // ...    
       }
   ```

   and `Sylius\RefundPlugin\Command\GenerateCreditMemo::shipments` method has been removed.

2. The constructor of the `Sylius\RefundPlugin\Command\RefundUnits` command has been changed:

   ```diff
       public function __construct(
           private string $orderNumber,
   -       /** @var array|OrderItemUnitRefund[] */
   +       /** @var array|UnitRefundInterface[] */
           private array $units,
   -       /** @var array|ShipmentRefund[] */
   -       private array $shipments,
   +       private int $paymentMethodId,
           private string $comment,
       ) {
           // ...
       }
   ```

   and `Sylius\RefundPlugin\Command\RefundUnits::shipments` method has been removed.

3. The constructor of the `Sylius\RefundPlugin\CommandHandler\RefundUnitsHandler` has been changed:

    ```diff
        public function __construct(
    -       private RefunderInterface $orderUnitsRefunder,
    -       private RefunderInterface $orderShipmentsRefunder,
    +       private iterable $refunders,
            private MessageBusInterface $eventBus,
            private OrderRepositoryInterface $orderRepository,
            private RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
        ) {
            // ...
        }
    ```
    
4. The `Sylius\RefundPlugin\Converter\LineItemsConverterInterface` interface implemented by
   `Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter` and `Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter`
   has been replaced with `Sylius\RefundPlugin\Converter\LineItemsConverterUnitRefundAwareInterface`.

5. The interface method `Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface::convert` has been changed:

   ```diff
   - public function convert(array $units, RefundTypeInterface $refundType, string $unitRefundClass): array;
   + public function convert(array $units, string $unitRefundClass): array;
   ```
   
6. The constructor of the `Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator` has been changed:

   ```diff
   -   public function __construct(private RefundUnitsConverterInterface $refundUnitsConverter)
   +   public function __construct(private RequestToRefundUnitsConverterInterface $requestToRefundUnitsConverter)
       {
           // ...
       }
   ```
   
7. The `Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface` interface has been replaced with
   `Sylius\RefundPlugin\Converter\RequestCommandCreatorInterface`.
   
8. The constructor of the `Sylius\RefundPlugin\Event\UnitsRefunded` event has been changed:

    ```diff
        public function __construct(
            private string $orderNumber,
    +       /** @var array|UnitRefundInterface[] */
            private array $units,
    -       private array $shipments,
            private int $paymentMethodId,
            private int $amount,
            private string $currencyCode,
            private string $comment,
        ) {
            // ...
        }
    ```

    and `Sylius\RefundPlugin\Event\UnitsRefunded::shipments` method has been removed.

9. The constructor of the `Sylius\RefundPlugin\Generator\CreditMemoGenerator` has been changed:

     ```diff
         public function __construct(
             private LineItemsConverterInterface $lineItemsConverter,
     -       private LineItemsConverterInterface $shipmentLineItemsConverter,
             private TaxItemsGeneratorInterface $taxItemsGenerator,
             private CreditMemoFactoryInterface $creditMemoFactory,
             private CustomerBillingDataFactoryInterface $customerBillingDataFactory,
             private ShopBillingDataFactoryInterface $shopBillingDataFactory,
         ) {
             // ...
         }
     ```
   
10. The interface method `Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface::generate` has been changed:

    ```diff
        public function generate(
            OrderInterface $order,
            int $total,
            array $units,
    -       array $shipments,
            string $comment
        ): CreditMemoInterface;
    ```

11. A static method has been added to the `Sylius\RefundPlugin\Model\UnitRefundInterface` interface:

    ```diff
    + public static function type(): RefundType;
    ```
    
12. The constructor of the `Sylius\RefundPlugin\Provider\RemainingTotalProvider` has been changed:

    ```diff
        public function __construct(
    -       private RepositoryInterface $orderItemUnitRepository,
    -       private RepositoryInterface $adjustmentRepository,
    +       private ServiceProviderInterface $refundUnitTotalProvider,
            private RepositoryInterface $refundRepository,
        ) {
            // ...
        }
    ```

13. The constructor of the `Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder` has been changed:

    ```diff
        public function __construct(
            private RefundCreatorInterface $refundCreator,
            private MessageBusInterface $eventBus,
    +       private UnitRefundFilterInterface $unitRefundFilter,
        ) {
            // ...
        }
    ```

14. The constructor of the `Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder` has been changed:

    ```diff
        public function __construct(
            private RefundCreatorInterface $refundCreator,
            private MessageBusInterface $eventBus,
    +       private UnitRefundFilterInterface $unitRefundFilter,
        ) {
            // ...
        }
    ```
    
15. The interface method `Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface` has been changed:

    ```diff
    - public function validateUnits(array $unitRefunds, RefundTypeInterface $refundType): void;
    + public function validateUnits(array $unitRefunds): void;
    ``` 
