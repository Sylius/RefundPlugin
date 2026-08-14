<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Action\Admin\DownloadCreditMemoAction as AdminDownloadCreditMemoAction;
use Sylius\RefundPlugin\Action\Admin\OrderRefundsListAction;
use Sylius\RefundPlugin\Action\Admin\RefundUnitsAction;
use Sylius\RefundPlugin\Action\Admin\SendCreditMemoAction;
use Sylius\RefundPlugin\Action\CompleteRefundPaymentAction;
use Sylius\RefundPlugin\Action\Shop\DownloadCreditMemoAction as ShopDownloadCreditMemoAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius_refund.controller.admin.download_credit_memo', AdminDownloadCreditMemoAction::class)
        ->args([
            service('sylius_refund.resolver.credit_memo_file'),
            service('sylius_refund.response_builder.credit_memo_file'),
            '%sylius_refund.pdf_generator.enabled%',
        ]);

    $services->set('sylius_refund.controller.shop.download_credit_memo', ShopDownloadCreditMemoAction::class)
        ->args([
            service('sylius_refund.resolver.credit_memo_file'),
            service('sylius_refund.checker.credit_memo_customer_relation'),
            service('sylius_refund.response_builder.credit_memo_file'),
            '%sylius_refund.pdf_generator.enabled%',
        ]);

    $services->set('sylius_refund.controller.admin.order_refunds_list', OrderRefundsListAction::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_refund.checker.order_refunds_list_availability'),
            service('sylius_refund.provider.refund_payment_methods'),
            service('twig'),
            service('request_stack'),
            service('router'),
        ]);

    $services->set('sylius_refund.controller.admin.refund_units', RefundUnitsAction::class)
        ->args([
            service('sylius.command_bus'),
            service('request_stack'),
            service('router'),
            service('sylius_refund.creator.request_command'),
            service('monolog.logger'),
            service('security.csrf.token_manager'),
        ]);

    $services->set('sylius_refund.controller.complete_refund_payment', CompleteRefundPaymentAction::class)
        ->args([
            service('request_stack'),
            service('sylius_refund.repository.refund_payment'),
            service('sylius.repository.order'),
            service('sylius_refund.state_resolver.refund_payment_completed_applier'),
            service('router'),
        ]);

    $services->set('sylius_refund.controller.admin.send_credit_memo', SendCreditMemoAction::class)
        ->args([
            service('sylius.command_bus'),
            service('sylius_refund.repository.credit_memo'),
            service('request_stack'),
            service('router'),
        ]);
};
