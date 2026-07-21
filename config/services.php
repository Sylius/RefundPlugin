<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/**/*.php');

    $parameters->set('sylius_refund.default_logo_file', '@SyliusRefundPlugin/assets/sylius-logo.png');
    $parameters->set('sylius_refund.template.logo_file', '%env(default:sylius_refund.default_logo_file:resolve:SYLIUS_REFUND_LOGO_FILE)%');

    $services->defaults()
        ->public();

    $services->set('sylius_refund.calculator.unit_refund_total', \Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculator::class)
        ->args([service('sylius_refund.provider.remaining_total')]);

    $services->alias(\Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface::class, 'sylius_refund.calculator.unit_refund_total');

    $services->set('sylius_refund.refunder.order_item_units', \Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder::class)
        ->args([
            service('sylius_refund.creator.refund'),
            service('sylius.event_bus'),
            service('sylius_refund.filter.unit_refund'),
        ])
        ->tag('sylius_refund.refunder');

    $services->set('sylius_refund.refunder.order_shipments', \Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder::class)
        ->args([
            service('sylius_refund.creator.refund'),
            service('sylius.event_bus'),
            service('sylius_refund.filter.unit_refund'),
        ])
        ->tag('sylius_refund.refunder');

    $services->set('sylius_refund.twig.extension.order_refunds', \Sylius\RefundPlugin\Twig\OrderRefundsExtension::class)
        ->args([
            service('sylius_refund.provider.order_refunded_total'),
            service(\Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface::class),
            service('sylius_refund.checker.unit_refunding_availability'),
            service('sylius.repository.order'),
            service('sylius_refund.repository.refund_payment'),
            service('sylius_refund.factory.refund_type'),
        ])
        ->tag('twig.extension');

    $services->set('sylius_refund.twig.extension.order_refund_availability', \Sylius\RefundPlugin\Twig\OrderRefundAvailabilityExtension::class)
        ->args([service('sylius_refund.checker.order_refunds_list_availability')])
        ->tag('twig.extension');

    $services->set('sylius_refund.repository.credit_memo_sequence', \Doctrine\ORM\EntityRepository::class)
        ->args([\Sylius\RefundPlugin\Entity\CreditMemoSequence::class])
        ->factory([service('doctrine.orm.entity_manager'), 'getRepository']);

    $services->set('sylius_refund.email_sender.credit_memo', \Sylius\RefundPlugin\Sender\CreditMemoEmailSender::class)
        ->args([
            service('sylius.email_sender'),
            '%sylius_refund.pdf_generator.enabled%',
            service('sylius_refund.resolver.credit_memo_file'),
            service('sylius_refund.resolver.credit_memo_file_path'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface::class, 'sylius_refund.email_sender.credit_memo');

    $services->set('sylius_refund.response_builder.credit_memo_file', \Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilder::class);

    $services->alias(\Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface::class, 'sylius_refund.response_builder.credit_memo_file');

    $services->set('sylius_refund.manager.credit_memo_file', \Sylius\RefundPlugin\Manager\CreditMemoFileManager::class)
        ->args([service('gaufrette.sylius_refund_credit_memo_filesystem')]);

    $services->alias(\Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface::class, 'sylius_refund.manager.credit_memo_file');
};
