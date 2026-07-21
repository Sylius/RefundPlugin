<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.resolver.credit_memo_file', \Sylius\RefundPlugin\Resolver\CreditMemoFileResolver::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.credit_memo_file'),
            service('sylius_refund.generator.credit_memo_pdf_file'),
            service('sylius_refund.manager.credit_memo_file'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface::class, 'sylius_refund.resolver.credit_memo_file');

    $services->set('sylius_refund.resolver.credit_memo_file_path', \Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolver::class)
        ->args(['%sylius_refund.credit_memo_save_path%']);

    $services->alias(\Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface::class, 'sylius_refund.resolver.credit_memo_file_path');
};
