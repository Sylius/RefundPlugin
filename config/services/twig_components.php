<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Twig\Component\OrderCreditMemosComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.twig_component.customer_credit_memos', OrderCreditMemosComponent::class)
        ->args([service('sylius_refund.repository.credit_memo')])
        ->tag('sylius.twig_component', ['key' => 'sylius_refund:shop:account:credit_memos'])
        ->tag('sylius.twig_component', ['key' => 'sylius_refund:admin:credit_memos']);
};
