<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.factory.credit_memo_sequence', \Sylius\RefundPlugin\Factory\CreditMemoSequenceFactory::class);

    $services->alias(\Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface::class, 'sylius_refund.factory.credit_memo_sequence');

    $services->set('sylius_refund.factory.refund_type', \Sylius\RefundPlugin\Factory\RefundTypeFactory::class)
        ->args(['%sylius_refund.refund_type%']);

    $services->alias(\Sylius\RefundPlugin\Factory\RefundTypeFactoryInterface::class, 'sylius_refund.factory.refund_type');

    $services->set('sylius_refund.factory.line_item', \Sylius\RefundPlugin\Factory\LineItemFactory::class)
        ->args(['%sylius_refund.model.line_item.class%']);

    $services->alias(\Sylius\RefundPlugin\Factory\LineItemFactoryInterface::class, 'sylius_refund.factory.line_item');

    $services->set('sylius_refund.custom_factory.credit_memo', \Sylius\RefundPlugin\Factory\CreditMemoFactory::class)
        ->decorate('sylius_refund.factory.credit_memo', null, 256)
        ->args([
            service('.inner'),
            service('sylius_refund.generator.credit_memo_identifier'),
            service('sylius_refund.generator.credit_memo_number'),
            service(\Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface::class),
        ]);

    $services->set('sylius_refund.custom_factory.shop_billing_data', \Sylius\RefundPlugin\Factory\ShopBillingDataFactory::class)
        ->decorate('sylius_refund.factory.shop_billing_data', null, 256)
        ->args([service('.inner')]);

    $services->set('sylius_refund.custom_factory.customer_billing_data', \Sylius\RefundPlugin\Factory\CustomerBillingDataFactory::class)
        ->decorate('sylius_refund.factory.customer_billing_data', null, 256)
        ->args([service('.inner')]);
};
