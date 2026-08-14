<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolver;
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolver;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.resolver.credit_memo_file', CreditMemoFileResolver::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.credit_memo_file'),
            service('sylius_refund.generator.credit_memo_pdf_file'),
            service('sylius_refund.manager.credit_memo_file'),
        ]);

    $services->alias(CreditMemoFileResolverInterface::class, 'sylius_refund.resolver.credit_memo_file');

    $services->set('sylius_refund.resolver.credit_memo_file_path', CreditMemoFilePathResolver::class)
        ->args(['%sylius_refund.credit_memo_save_path%']);

    $services->alias(CreditMemoFilePathResolverInterface::class, 'sylius_refund.resolver.credit_memo_file_path');
};
