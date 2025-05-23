### UPGRADE FROM 2.0.1 TO 2.0.2

1. From this version, the plugin now validates if an Order can transition to either `refund` or `partially_refund` before performing or rendering refund operations.

   - In the controller, both transitions (`refund` and `partially_refund`) are checked. If neither transition is allowed, a 403 Forbidden is thrown.
   - In the Twig template, the refund button is displayed only if at least one of the transitions is available.

1. The following constructor signatures have been changed:

   `Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityChecker`:
    ```diff
    public function __construct(
            private OrderRepositoryInterface $orderRepository,
    +       private ?StateMachineInterface $stateMachine = null,
    )
    ```

   `Sylius\RefundPlugin\Checker\OrderRefundsListAvailabilityChecker`:
    ```diff
    public function __construct(
            private OrderRepositoryInterface $orderRepository,
    +       private ?OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker = null,
    )
    ```

### UPGRADE FROM 1.X TO 2.0

1. Support for Sylius 2.0 has been added, it is now the recommended Sylius version to use with SyliusRefundPlugin.

1. Support for Sylius 1.X has been dropped, upgrade your application to [Sylius 2.0](https://github.com/Sylius/Sylius/blob/2.0/UPGRADE-2.0.md).

1. The minimum supported version of PHP has been increased to 8.2.

1. The way of customizing resource definition has been changed.

    ```diff
    -   sylius_resource:
    +   sylius_refund:
            resources:
    -           sylius_refund.sample_resource:
    +           sample_resource:
                    ...
    ```  

1. Doctrine migrations have been regenerated, meaning all previous migration files have been removed and their content is now in a single migration file.
   To apply the new migration and get rid of the old entries run migrations as usual:

   ```bash
       bin/console doctrine:migrations:migrate --no-interaction
   ```

1. As the assets have been reorganized, you need to update the way you import them in your application.
   Add the following line to your `assets/admin/entrypoint.js` file:

    ```js
        import '../../vendor/sylius/refund-plugin/assets/entrypoint';
    ```

1. The structures of the directories have been updated to follow the current Symfony recommendations:
   - `@SyliusRefundPlugin/Resources/assets` -> `@SyliusRefundPlugin/assets`
   - `@SyliusRefundPlugin/Resources/config` -> `@SyliusRefundPlugin/config`
   - `@SyliusRefundPlugin/Resources/translations` -> `@SyliusRefundPlugin/translations`
   - `@SyliusRefundPlugin/Resources/views` -> `@SyliusRefundPlugin/templates`

   You need to adjust the import of configuration file in your end application:

   ```diff
   imports:
   -   - { resource: "@SyliusRefundPlugin/Resources/config/config.yml" }
   +   - { resource: '@SyliusRefundPlugin/config/config.yaml' }
   ```

   And the routes configuration paths:

   ```diff
       sylius_refund:
   -       resource: "@SyliusRefundPlugin/Resources/config/routing.yml"
   +       resource: "@SyliusRefundPlugin/config/routes.yaml"
   ```

   Adjust the paths to assets and templates if you are using them.

1. No need to overwrite templates:  
   Thanks to the use of Twig Hooks and the refactoring of templates, you no longer need to overwrite templates to use plugin features.

1. The following routes have been removed:  
    - `sylius_refund_order_credit_memos_list`
    - `sylius_refund_plugin_shop_order_credit_memos_partial`

1. Aliases introduced in RefundPlugin 1.6 have now become the primary service IDs in RefundPlugin 2.0.
   The old service IDs have been removed, and all references must be updated accordingly:

   | Old ID                                                                            | New ID                                                                              |
   |-----------------------------------------------------------------------------------|-------------------------------------------------------------------------------------|
   | `sylius_refund_plugin.repository.credit_memo_sequence`                            | `sylius_refund.repository.credit_memo_sequence`                                     |
   | `Sylius\RefundPlugin\Action\Admin\DownloadCreditMemoAction`                       | `sylius_refund.controller.admin.download_credit_memo`                               |
   | `Sylius\RefundPlugin\Action\Admin\OrderRefundsListAction`                         | `sylius_refund.controller.admin.order_refunds_list`                                 |
   | `Sylius\RefundPlugin\Action\Admin\RefundUnitsAction`                              | `sylius_refund.controller.admin.refund_units`                                       |
   | `Sylius\RefundPlugin\Action\Admin\SendCreditMemoAction`                           | `sylius_refund.controller.admin.send_credit_memo`                                   |
   | `Sylius\RefundPlugin\Action\CompleteRefundPaymentAction`                          | `sylius_refund.controller.complete_refund_payment`                                  |
   | `Sylius\RefundPlugin\Action\Shop\DownloadCreditMemoAction`                        | `sylius_refund.controller.shop.download_credit_memo`                                |
   | `Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityChecker`                   | `sylius_refund.checker.order_refunding_availability`                                |
   | `Sylius\RefundPlugin\Checker\OrderRefundsListAvailabilityChecker`                 | `sylius_refund.checker.order_refunds_list_availability`                             |
   | `Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler`                    | `sylius_refund.command_handler.generate_credit_memo`                                |
   | `Sylius\RefundPlugin\CommandHandler\RefundUnitsHandler`                           | `sylius_refund.command_handler.refund_units`                                        |
   | `Sylius\RefundPlugin\CommandHandler\SendCreditMemoHandler`                        | `sylius_refund.command_handler.send_credit_memo`                                    |
   | `Sylius\RefundPlugin\Converter\LineItem\OrderItemUnitLineItemsConverter`          | `sylius_refund.converter.line_items.order_item_unit`                                |
   | `Sylius\RefundPlugin\Converter\LineItem\ShipmentLineItemsConverter`               | `sylius_refund.converter.line_items.shipment`                                       |
   | `Sylius\RefundPlugin\Doctrine\ORM\CountOrderItemUnitRefundsBelongingToOrderQuery` | `sylius_refund.doctrine.orm.query.count_order_item_unit_refunds_belonging_to_order` |
   | `Sylius\RefundPlugin\Doctrine\ORM\CountShipmentRefundsBelongingToOrderQuery`      | `sylius_refund.doctrine.orm.query.count_shipment_refunds_belonging_to_order`        |
   | `Sylius\RefundPlugin\Factory\CreditMemoFactory`                                   | `sylius_refund.custom_factory.credit_memo`                                          |
   | `Sylius\RefundPlugin\Factory\CustomerBillingDataFactory`                          | `sylius_refund.custom_factory.customer_billing_data`                                |
   | `Sylius\RefundPlugin\Factory\ShopBillingDataFactory`                              | `sylius_refund.custom_factory.shop_billing_data`                                    |
   | `Sylius\RefundPlugin\Listener\CreditMemoGeneratedEventListener`                   | `sylius_refund.listener.credit_memo_generated`                                      |
   | `Sylius\RefundPlugin\Listener\UnitRefundedEventListener`                          | `sylius_refund.listener.unit_refunded`                                              |
   | `Sylius\RefundPlugin\Menu\AdminMainMenuListener`                                  | `sylius_refund.listener.admin_main_menu`                                            |
   | `Sylius\RefundPlugin\Menu\OrderShowMenuListener`                                  | `sylius_refund.listener.order_show_menu`                                            |
   | `Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager`                     | `sylius_refund.process_manager.credit_memo`                                         |
   | `Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager`                  | `sylius_refund.process_manager.refund_payment`                                      |
   | `Sylius\RefundPlugin\Provider\OrderItemUnitTotalProvider`                         | `sylius_refund.provider.order_item_unit_total`                                      |
   | `Sylius\RefundPlugin\Provider\ShipmentTotalProvider`                              | `sylius_refund.provider.shipment_total`                                             |
   | `Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder`                             | `sylius_refund.refunder.order_item_units`                                           |
   | `Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder`                             | `sylius_refund.refunder.order_shipments`                                            |
   | `Sylius\RefundPlugin\Twig\OrderRefundsExtension`                                  | `sylius_refund.twig.extension.order_refunds`                                        |
   | `Sylius\RefundPlugin\Validator\OrderItemUnitRefundsBelongingToOrderValidator`     | `sylius_refund.validator.order_item_unit_refunds_belonging_to_order`                |
   | `Sylius\RefundPlugin\Validator\ShipmentRefundsBelongingToOrderValidator`          | `sylius_refund.validator.shipment_refunds_belonging_to_order`                       |

1. The following services had new aliases added in RefundPlugin 1.6. In RefundPlugin 2.0, these aliases have become
   the primary service IDs, and the old service IDs remain as aliases:

   | Old ID                                                                           | New Id                                                          |
   |----------------------------------------------------------------------------------|-----------------------------------------------------------------|
   | `Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface`              | `sylius_refund.calculator.unit_refund_total`                    |
   | `Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface`         | `sylius_refund.checker.credit_memo_customer_relation`           |
   | `Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface`            | `sylius_refund.checker.order_fully_refunded_total`              |
   | `Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface`          | `sylius_refund.checker.unit_refunding_availability`             |
   | `Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface`             | `sylius_refund.converter.line_items`                            |
   | `Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface`                    | `sylius_refund.converter.refund_units`                          |
   | `Sylius\RefundPlugin\Converter\Request\RequestToOrderItemUnitRefundConverter`    | `sylius_refund.converter.request_to_order_item_unit_refund`     |
   | `Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface`   | `sylius_refund.converter.request_to_refund_units`               |
   | `Sylius\RefundPlugin\Converter\Request\RequestToShipmentRefundConverter`         | `sylius_refund.converter.request_to_shipment_refund`            |
   | `Sylius\RefundPlugin\Creator\RefundCreatorInterface`                             | `sylius_refund.creator.refund`                                  |
   | `Sylius\RefundPlugin\Creator\RequestCommandCreatorInterface`                     | `sylius_refund.creator.request_command`                         |
   | `Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface`                 | `sylius_refund.factory.credit_memo_sequence`                    |
   | `Sylius\RefundPlugin\Factory\LineItemFactoryInterface`                           | `sylius_refund.factory.line_item`                               |
   | `Sylius\RefundPlugin\Factory\RefundTypeFactoryInterface`                         | `sylius_refund.factory.refund_type`                             |
   | `Sylius\RefundPlugin\Filter\UnitRefundFilterInterface`                           | `sylius_refund.filter.unit_refund`                              |
   | `Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface`             | `sylius_refund.generator.credit_memo_file_name`                 |
   | `Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface`                     | `sylius_refund.generator.credit_memo`                           |
   | `Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface`           | `sylius_refund.generator.credit_memo_identifier`                |
   | `Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface`               | `sylius_refund.generator.credit_memo_number`                    |
   | `Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface`              | `sylius_refund.generator.credit_memo_pdf_file`                  |
   | `Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface`                     | `sylius_refund.generator.pdf_options`                           |
   | `Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface`                       | `sylius_refund.generator.tax_items`                             |
   | `Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface`                      | `sylius_refund.generator.twig_to_pdf`                           |
   | `Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface`                     | `sylius_refund.manager.credit_memo_file`                        |
   | `Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManagerInterface`        | `sylius_refund.process_manager.units_refunded`                  |
   | `Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface`                   | `sylius_refund.provider.credit_memo_file`                       |
   | `Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface`         | `sylius_refund.provider.current_date_time_immutable`            |
   | `Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface`               | `sylius_refund.provider.order_refunded_total`                   |
   | `Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface`              | `sylius_refund.provider.refunded_shipment_fee`                  |
   | `Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface`             | `sylius_refund.provider.refund_payment_methods`                 |
   | `Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface`                 | `sylius_refund.provider.related_payment_id`                     |
   | `Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface`                   | `sylius_refund.provider.remaining_total`                        |
   | `Sylius\RefundPlugin\Provider\TaxRateProviderInterface`                          | `sylius_refund.provider.tax_rate`                               |
   | `Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface`               | `sylius_refund.resolver.credit_memo_file_path`                  |
   | `Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface`                   | `sylius_refund.resolver.credit_memo_file`                       |
   | `Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface`     | `sylius_refund.response_builder.credit_memo_file`               |
   | `Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface`                      | `sylius_refund.email_sender.credit_memo`                        |
   | `Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface`     | `sylius_refund.state_resolver.order_fully_refunded`             |
   | `Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface` | `sylius_refund.state_resolver.order_partially_refunded`         |
   | `Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface`  | `sylius_refund.state_resolver.refund_payment_completed_applier` |
   | `Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface`                   | `sylius_refund.validator.refund_amount`                         |
   | `Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface`             | `sylius_refund.validator.refund_units_command`                  |

1. The following deprecated aliases have been removed, use the service IDs instead:
   
   | Old alias ID                                                     | Service Id                                            | 
   |------------------------------------------------------------------|-------------------------------------------------------|
   | `Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculator`       | `sylius_refund.calculator.unit_refund_total`          |
   | `Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationChecker`  | `sylius_refund.checker.credit_memo_customer_relation` |
   | `Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalChecker`     | `sylius_refund.checker.order_fully_refunded_total`    |
   | `Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityChecker`  | `sylius_refund.checker.order_refunding_availability`  |
   | `Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityChecker`   | `sylius_refund.checker.unit_refunding_availability`   |
   | `Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter`  | `sylius_refund.converter.line_items.order_item_unit`  |
   | `Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter`       | `sylius_refund.converter.line_items.shipment`         |
   | `Sylius\RefundPlugin\Creator\RefundCreator`                      | `sylius_refund.creator.refund`                        |
   | `Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator`          | `sylius_refund.creator.request_command`               |
   | `Sylius\RefundPlugin\Factory\CreditMemoSequenceFactory`          | `sylius_refund.factory.credit_memo_sequence`          |
   | `Sylius\RefundPlugin\Generator\CreditMemoGenerator`              | `sylius_refund.generator.credit_memo`                 |
   | `Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator`       | `sylius_refund.generator.credit_memo_pdf_file`        |
   | `Sylius\RefundPlugin\Generator\SequentialCreditMemoNumberGenerator`| `sylius_refund.generator.credit_memo_number`          |
   | `Sylius\RefundPlugin\Generator\UuidCreditMemoIdentifierGenerator`| `sylius_refund.generator.credit_memo_identifier`      |
   | `Sylius\RefundPlugin\Provider\OrderRefundedTotalProvider`        | `sylius_refund.provider.order_refunded_total`         |
   | `Sylius\RefundPlugin\Provider\RefundedShipmentFeeProvider`       | `sylius_refund.provider.refunded_shipment_fee`        |
   | `Sylius\RefundPlugin\Provider\RemainingTotalProvider`            | `sylius_refund.provider.remaining_total`              |
   | `Sylius\RefundPlugin\Provider\UnitRefundedTotalProvider`         | `sylius_refund.provider.unit_refunded_total`          |
   | `Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilder` | `sylius_refund.response_builder.credit_memo_file`   |
   | `Sylius\RefundPlugin\Sender\CreditMemoEmailSender`               | `sylius_refund.email_sender.credit_memo`              |
   | `Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolver` | `sylius_refund.state_resolver.order_fully_refunded` |
   | `Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolver` | `sylius_refund.state_resolver.order_partially_refunded` |
   | `Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplier` | `sylius_refund.state_resolver.refund_payment_completed` |
   | `Sylius\RefundPlugin\Validator\RefundAmountValidator`            | `sylius_refund.validator.refund_amount`               |
   | `Sylius\RefundPlugin\Validator\RefundUnitsCommandValidator`      | `sylius_refund.validator.refund_units_command`        |

1. The following parameters have been renamed:

   | Old parameter                      | New parameter                      |  
   |------------------------------------|------------------------------------|
   | `default_logo_file`                | `sylius_refund.default_logo_file`  |
   | `sylius.refund.template.logo_file` | `sylius_refund.template.logo_file` |

1. The following configuration parameters have been renamed:

    ```diff
    -   sylius_refund_plugin:
    +   sylius_refund:
            pdf_generator:
                ...
    ```

1. The buses `sylius_refund_plugin.command_bus` and `sylius_refund_plugin.event_bus` have been replaced
   accordingly by `sylius.command_bus` and `sylius.event_bus`.

1. The visibility of services has been changed to `private` by default. This change enhances the performance
   and maintainability of the application and also follows Symfony's best practices for service encapsulation.

   Exceptions:
   - Services required by Symfony to be `public` (e.g., controllers, event listeners) remain public.

1. `_javascript.html.twig` file has been removed, and its code has been moved to `src/Resources/assets/js/refund-button.js`. When upgrading to 2.0, import the `src/Resources/assets/entrypoint.js` file into your application’s main js file.

   1. The following constructor signatures have been changed:

      - `Sylius\RefundPlugin\Action\Admin\OrderRefundsListAction`:
          ```diff
          public function __construct(
          -       private readonly SessionInterface | RequestStack $requestStackOrSession,
          +       private RequestStack $requestStack,
                  private OrderRepositoryInterface $orderRepository,
                  private OrderRefundingAvailabilityCheckerInterface $orderRefundsListAvailabilityChecker,
                  private RefundPaymentMethodsProviderInterface $refundPaymentMethodsProvider,
                  private Environment $twig,
                  private UrlGeneratorInterface $router,
          ) {
          }
          ```

      - `Sylius\RefundPlugin\Action\Admin\RefundUnitsAction`:
          ```diff
          public function __construct(
          -       private readonly SessionInterface|RequestStack $requestStackOrSession,
          -       private readonly RequestCommandCreatorInterface|RefundUnitsCommandCreatorInterface $commandCreator,
          +       private RequestStack $requestStack,
          +       private RequestCommandCreatorInterface $commandCreator,
                  private MessageBusInterface $commandBus,
                  private UrlGeneratorInterface $router,
                  private LoggerInterface $logger,
                  private CsrfTokenManagerInterface $csrfTokenManager,
          ) {
          }
          ```

      - `Sylius\RefundPlugin\Action\Admin\SendCreditMemoAction`:
          ```diff
          public function __construct(
          -       private readonly SessionInterface | RequestStack $requestStackOrSession,
          +       private RequestStack $requestStack,
                  private MessageBusInterface $commandBus,
                  private RepositoryInterface $creditMemoRepository,
                  private UrlGeneratorInterface $router,
          ) {
          }
          ```

         - `Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler`:
             ```diff
             public function __construct(
             -       private readonly ObjectManager $creditMemoManager,
             -       private readonly ?CreditMemoFileResolverInterface $creditMemoFileResolver = null,
             +       private EntityManagerInterface $creditMemoManager,
             +       private CreditMemoFileResolverInterface $creditMemoFileResolver,
                     private CreditMemoIdentifierGeneratorInterface $identifierGenerator,
                     private CreditMemoNumberGeneratorInterface $numberGenerator,
                     private CreditMemoPdfFileGeneratorInterface $pdfFileGenerator,
             ) {
             }
             ```

         - `Sylius\RefundPlugin\CommandHandler\RefundUnitsHandler`:
           ```diff
           public function __construct(
           -    private ObjectManager $entityManager,
           +    private EntityManagerInterface $entityManager,
               private RefundCreatorInterface $refundCreator,
               private RemainingTotalProviderInterface $remainingTotalProvider,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Converter\LineItem\OrderItemUnitLineItemsConverter`:
           ```diff
           public function __construct(
               private RepositoryInterface $orderItemUnitRepository,
               private TaxRateProviderInterface $taxRateProvider,
           -       private readonly ?LineItemFactoryInterface $lineItemFactory = null,
           +       private LineItemFactoryInterface $lineItemFactory,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Converter\LineItem\ShipmentLineItemsConverter`:
           ```diff
           public function __construct(
               private RepositoryInterface $adjustmentRepository,
               private TaxRateProviderInterface $taxRateProvider,
           -       private readonly ?LineItemFactoryInterface $lineItemFactory = null,
           +       private LineItemFactoryInterface $lineItemFactory,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Creator\RefundCreator`:
           ```diff
           public function __construct(
               private RefundFactoryInterface $refundFactory,
               private RemainingTotalProviderInterface $remainingTotalProvider,
               private OrderRepositoryInterface $orderRepository,
           -   private ObjectManager $refundManager,
           +   private EntityManagerInterface $refundManager,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator`:
           ```diff
           public function __construct(
           -       private RequestToRefundUnitsConverterInterface|RefundUnitsConverterInterface $requestToRefundUnitsConverter,
           +       private RequestToRefundUnitsConverterInterface $requestToRefundUnitsConverter,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Generator\CreditMemoGenerator`:
           ```diff
           public function __construct(
           -   private LineItemsConverterInterface|LegacyLineItemsConverterInterface $lineItemsConverter,
           +   private LineItemsConverterInterface $lineItemsConverter,
               private TaxItemsGeneratorInterface|LineItemsConverterInterface $taxItemsGenerator,
               private CreditMemoFactoryInterface|TaxItemsGeneratorInterface $creditMemoFactory,
               private CustomerBillingDataFactoryInterface|CreditMemoFactoryInterface $customerBillingDataFactory,
               private ShopBillingDataFactoryInterface|CustomerBillingDataFactoryInterface $shopBillingDataFactory,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Provider\RemainingTotalProvider`:
           ```diff
           public function __construct(
           -   private ServiceProviderInterface|RepositoryInterface $refundUnitTotalProvider,
           +   private ServiceProviderInterface $refundUnitTotalProvider,
               private RepositoryInterface $refundRepository,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder`:
           ```diff
           public function __construct(
               private RefundCreatorInterface $refundCreator,
               private MessageBusInterface $eventBus,
           -   private ?UnitRefundFilterInterface $unitRefundFilter = null,
           +   private UnitRefundFilterInterface $unitRefundFilter,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder`:
           ```diff
           public function __construct(
               private RefundCreatorInterface $refundCreator,
               private MessageBusInterface $eventBus,
           -   private ?UnitRefundFilterInterface $unitRefundFilter = null,
           +   private UnitRefundFilterInterface $unitRefundFilter,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Sender\CreditMemoEmailSender`:
           ```diff
           public function __construct(
           -       private readonly ?CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
           -       private readonly ?FileManagerInterface $fileManager,
           -       private readonly ?CreditMemoFileResolverInterface $creditMemoFileResolver = null,
           -       private readonly ?CreditMemoFilePathResolverInterface $creditMemoFilePathResolver = null,
           +       private CreditMemoFileResolverInterface $creditMemoFileResolver,
           +       private CreditMemoFilePathResolverInterface $creditMemoFilePathResolver,
           ) {
           }
           ```

         - `Sylius\RefundPlugin\Validator\RefundUnitsCommandValidator`:
           ```diff
           public function __construct(
               private OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
               private RefundAmountValidatorInterface $refundAmountValidator,
           -   private ?iterable $refundUnitsBelongingToOrderValidators = null,
           +   private iterable $refundUnitsBelongingToOrderValidators,
           ) {
           }
           ```   

1. The following deprecated classes and interfaces have been removed in 2.0:

   - Sylius\RefundPlugin\Converter\LineItemsConverterInterface
   - Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter
   - Sylius\RefundPlugin\Converter\RequestToOrderItemUnitRefundConverter
   - Sylius\RefundPlugin\Converter\RequestToRefundUnitsConverterInterface
   - Sylius\RefundPlugin\Converter\RequestToShipmentRefundConverter
   - Sylius\RefundPlugin\Converter\ShipmentLineItemsConverter
   - Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface
   - Sylius\RefundPlugin\File\FileManagerInterface
   - Sylius\RefundPlugin\File\TemporaryFileManager
   - Sylius\RefundPlugin\Menu\OrderShowMenuListener
