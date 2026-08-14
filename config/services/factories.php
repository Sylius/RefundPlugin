<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Factory\CreditMemoFactory;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactory;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactory;
use Sylius\RefundPlugin\Factory\LineItemFactory;
use Sylius\RefundPlugin\Factory\LineItemFactoryInterface;
use Sylius\RefundPlugin\Factory\RefundTypeFactory;
use Sylius\RefundPlugin\Factory\RefundTypeFactoryInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactory;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.factory.credit_memo_sequence', CreditMemoSequenceFactory::class);

    $services->alias(CreditMemoSequenceFactoryInterface::class, 'sylius_refund.factory.credit_memo_sequence');

    $services->set('sylius_refund.factory.refund_type', RefundTypeFactory::class)
        ->args(['%sylius_refund.refund_type%']);

    $services->alias(RefundTypeFactoryInterface::class, 'sylius_refund.factory.refund_type');

    $services->set('sylius_refund.factory.line_item', LineItemFactory::class)
        ->args(['%sylius_refund.model.line_item.class%']);

    $services->alias(LineItemFactoryInterface::class, 'sylius_refund.factory.line_item');

    $services->set('sylius_refund.custom_factory.credit_memo', CreditMemoFactory::class)
        ->decorate('sylius_refund.factory.credit_memo', null, 256)
        ->args([
            service('.inner'),
            service('sylius_refund.generator.credit_memo_identifier'),
            service('sylius_refund.generator.credit_memo_number'),
            service(CurrentDateTimeImmutableProviderInterface::class),
        ]);

    $services->set('sylius_refund.custom_factory.shop_billing_data', ShopBillingDataFactory::class)
        ->decorate('sylius_refund.factory.shop_billing_data', null, 256)
        ->args([service('.inner')]);

    $services->set('sylius_refund.custom_factory.customer_billing_data', CustomerBillingDataFactory::class)
        ->decorate('sylius_refund.factory.customer_billing_data', null, 256)
        ->args([service('.inner')]);
};
