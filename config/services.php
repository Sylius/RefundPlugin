<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityRepository;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculator;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Manager\CreditMemoFileManager;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface;
use Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder;
use Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilder;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSender;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;
use Sylius\RefundPlugin\Twig\OrderRefundAvailabilityExtension;
use Sylius\RefundPlugin\Twig\OrderRefundsExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/**/*.php');

    $parameters->set('sylius_refund.default_logo_file', '@SyliusRefundPlugin/assets/sylius-logo.png');
    $parameters->set('sylius_refund.template.logo_file', '%env(default:sylius_refund.default_logo_file:resolve:SYLIUS_REFUND_LOGO_FILE)%');

    $services->defaults()
        ->public();

    $services->set('sylius_refund.calculator.unit_refund_total', UnitRefundTotalCalculator::class)
        ->args([service('sylius_refund.provider.remaining_total')]);

    $services->alias(UnitRefundTotalCalculatorInterface::class, 'sylius_refund.calculator.unit_refund_total');

    $services->set('sylius_refund.refunder.order_item_units', OrderItemUnitsRefunder::class)
        ->args([
            service('sylius_refund.creator.refund'),
            service('sylius.event_bus'),
            service('sylius_refund.filter.unit_refund'),
        ])
        ->tag('sylius_refund.refunder');

    $services->set('sylius_refund.refunder.order_shipments', OrderShipmentsRefunder::class)
        ->args([
            service('sylius_refund.creator.refund'),
            service('sylius.event_bus'),
            service('sylius_refund.filter.unit_refund'),
        ])
        ->tag('sylius_refund.refunder');

    $services->set('sylius_refund.twig.extension.order_refunds', OrderRefundsExtension::class)
        ->args([
            service('sylius_refund.provider.order_refunded_total'),
            service(UnitRefundedTotalProviderInterface::class),
            service('sylius_refund.checker.unit_refunding_availability'),
            service('sylius.repository.order'),
            service('sylius_refund.repository.refund_payment'),
            service('sylius_refund.factory.refund_type'),
        ])
        ->tag('twig.extension');

    $services->set('sylius_refund.twig.extension.order_refund_availability', OrderRefundAvailabilityExtension::class)
        ->args([service('sylius_refund.checker.order_refunds_list_availability')])
        ->tag('twig.extension');

    $services->set('sylius_refund.repository.credit_memo_sequence', EntityRepository::class)
        ->args([CreditMemoSequence::class])
        ->factory([service('doctrine.orm.entity_manager'), 'getRepository']);

    $services->set('sylius_refund.email_sender.credit_memo', CreditMemoEmailSender::class)
        ->args([
            service('sylius.email_sender'),
            '%sylius_refund.pdf_generator.enabled%',
            service('sylius_refund.resolver.credit_memo_file'),
            service('sylius_refund.resolver.credit_memo_file_path'),
        ]);

    $services->alias(CreditMemoEmailSenderInterface::class, 'sylius_refund.email_sender.credit_memo');

    $services->set('sylius_refund.response_builder.credit_memo_file', CreditMemoFileResponseBuilder::class);

    $services->alias(CreditMemoFileResponseBuilderInterface::class, 'sylius_refund.response_builder.credit_memo_file');

    // Registered directly instead of through knp_gaufrette's bundle config, since
    // knplabs/knp-gaufrette-bundle was removed from Sylius as of 2.3. The underlying
    // knplabs/gaufrette library has no Symfony dependency, so it keeps working unchanged.
    $services->set('gaufrette.sylius_refund_credit_memo_filesystem', Filesystem::class)
        ->args([service('sylius_refund.gaufrette.adapter.credit_memo')]);

    $services->set('sylius_refund.gaufrette.adapter.credit_memo', Local::class)
        ->args(['%sylius_refund.credit_memo_save_path%', true]);

    $services->set('sylius_refund.manager.credit_memo_file', CreditMemoFileManager::class)
        ->args([service('gaufrette.sylius_refund_credit_memo_filesystem')])
        ->deprecate('sylius/refund-plugin', '2.1', 'The "%service_id%" service is deprecated. Use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface from sylius/pdf-generation-bundle instead.');

    $services->alias(CreditMemoFileManagerInterface::class, 'sylius_refund.manager.credit_memo_file');
};
