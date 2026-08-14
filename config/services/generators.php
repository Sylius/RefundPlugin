<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Generator\CreditMemoFileNameGenerator;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoGenerator;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Generator\PdfOptionsGenerator;
use Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface;
use Sylius\RefundPlugin\Generator\SequentialCreditMemoNumberGenerator;
use Sylius\RefundPlugin\Generator\TaxItemsGenerator;
use Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface;
use Sylius\RefundPlugin\Generator\TwigToPdfGenerator;
use Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\RefundPlugin\Generator\UuidCreditMemoIdentifierGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.generator.credit_memo_number', SequentialCreditMemoNumberGenerator::class)
        ->args([
            service('sylius_refund.repository.credit_memo_sequence'),
            service('sylius_refund.factory.credit_memo_sequence'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->alias(CreditMemoNumberGeneratorInterface::class, 'sylius_refund.generator.credit_memo_number');

    $services->set('sylius_refund.generator.credit_memo', CreditMemoGenerator::class)
        ->args([
            service('sylius_refund.converter.line_items'),
            service('sylius_refund.generator.tax_items'),
            service('sylius_refund.factory.credit_memo'),
            service('sylius_refund.factory.customer_billing_data'),
            service('sylius_refund.factory.shop_billing_data'),
        ]);

    $services->alias(CreditMemoGeneratorInterface::class, 'sylius_refund.generator.credit_memo');

    $services->set('sylius_refund.generator.credit_memo_file_name', CreditMemoFileNameGenerator::class);

    $services->alias(CreditMemoFileNameGeneratorInterface::class, 'sylius_refund.generator.credit_memo_file_name');

    $services->set('sylius_refund.generator.credit_memo_pdf_file', CreditMemoPdfFileGenerator::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('file_locator'),
            '@SyliusRefundPlugin/download/credit_memo.html.twig',
            '%sylius_refund.template.logo_file%',
            service('sylius_refund.generator.twig_to_pdf'),
            service('sylius_refund.generator.credit_memo_file_name'),
        ]);

    $services->alias(CreditMemoPdfFileGeneratorInterface::class, 'sylius_refund.generator.credit_memo_pdf_file');

    $services->set('sylius_refund.generator.tax_items', TaxItemsGenerator::class);

    $services->alias(TaxItemsGeneratorInterface::class, 'sylius_refund.generator.tax_items');

    $services->set('sylius_refund.generator.credit_memo_identifier', UuidCreditMemoIdentifierGenerator::class);

    $services->alias(CreditMemoIdentifierGeneratorInterface::class, 'sylius_refund.generator.credit_memo_identifier');

    $services->set('sylius_refund.generator.pdf_options', PdfOptionsGenerator::class)
        ->args([
            service('file_locator'),
            '%knp_snappy.pdf.options%',
            '%sylius_refund.pdf_generator.allowed_files%',
        ])
        ->deprecate('sylius/refund-plugin', '2.1', 'The "%service_id%" service is deprecated. PDF options are now handled by sylius/pdf-generation-bundle adapters.');

    $services->alias(PdfOptionsGeneratorInterface::class, 'sylius_refund.generator.pdf_options');

    $services->set('sylius_refund.generator.twig_to_pdf', TwigToPdfGenerator::class)
        ->args([
            service('twig'),
            service('knp_snappy.pdf'),
            service('sylius_refund.generator.pdf_options'),
        ])
        ->deprecate('sylius/refund-plugin', '2.1', 'The "%service_id%" service is deprecated. Use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface from sylius/pdf-generation-bundle instead.');

    $services->alias(TwigToPdfGeneratorInterface::class, 'sylius_refund.generator.twig_to_pdf');
};
