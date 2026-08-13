<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.generator.credit_memo_number', \Sylius\RefundPlugin\Generator\SequentialCreditMemoNumberGenerator::class)
        ->args([
            service('sylius_refund.repository.credit_memo_sequence'),
            service('sylius_refund.factory.credit_memo_sequence'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface::class, 'sylius_refund.generator.credit_memo_number');

    $services->set('sylius_refund.generator.credit_memo', \Sylius\RefundPlugin\Generator\CreditMemoGenerator::class)
        ->args([
            service('sylius_refund.converter.line_items'),
            service('sylius_refund.generator.tax_items'),
            service('sylius_refund.factory.credit_memo'),
            service('sylius_refund.factory.customer_billing_data'),
            service('sylius_refund.factory.shop_billing_data'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface::class, 'sylius_refund.generator.credit_memo');

    $services->set('sylius_refund.generator.credit_memo_file_name', \Sylius\RefundPlugin\Generator\CreditMemoFileNameGenerator::class);

    $services->alias(\Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface::class, 'sylius_refund.generator.credit_memo_file_name');

    $services->set('sylius_refund.generator.credit_memo_pdf_file', \Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('file_locator'),
            '@SyliusRefundPlugin/download/credit_memo.html.twig',
            '%sylius_refund.template.logo_file%',
            service('sylius_refund.generator.twig_to_pdf'),
            service('sylius_refund.generator.credit_memo_file_name'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface::class, 'sylius_refund.generator.credit_memo_pdf_file');

    $services->set('sylius_refund.generator.tax_items', \Sylius\RefundPlugin\Generator\TaxItemsGenerator::class);

    $services->alias(\Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface::class, 'sylius_refund.generator.tax_items');

    $services->set('sylius_refund.generator.credit_memo_identifier', \Sylius\RefundPlugin\Generator\UuidCreditMemoIdentifierGenerator::class);

    $services->alias(\Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface::class, 'sylius_refund.generator.credit_memo_identifier');

    $services->set('sylius_refund.generator.pdf_options', \Sylius\RefundPlugin\Generator\PdfOptionsGenerator::class)
        ->args([
            service('file_locator'),
            '%knp_snappy.pdf.options%',
            '%sylius_refund.pdf_generator.allowed_files%',
        ])
        ->deprecate('sylius/refund-plugin', '2.1', 'The "%service_id%" service is deprecated. PDF options are now handled by sylius/pdf-generation-bundle adapters.');

    $services->alias(\Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface::class, 'sylius_refund.generator.pdf_options');

    $services->set('sylius_refund.generator.twig_to_pdf', \Sylius\RefundPlugin\Generator\TwigToPdfGenerator::class)
        ->args([
            service('twig'),
            service('knp_snappy.pdf'),
            service('sylius_refund.generator.pdf_options'),
        ])
        ->deprecate('sylius/refund-plugin', '2.1', 'The "%service_id%" service is deprecated. Use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface from sylius/pdf-generation-bundle instead.');

    $services->alias(\Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface::class, 'sylius_refund.generator.twig_to_pdf');
};
