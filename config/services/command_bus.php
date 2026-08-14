<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler;
use Sylius\RefundPlugin\CommandHandler\RefundUnitsHandler;
use Sylius\RefundPlugin\CommandHandler\SendCreditMemoHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.command_handler.refund_units', RefundUnitsHandler::class)
        ->args([
            tagged_iterator('sylius_refund.refunder'),
            service('sylius.event_bus'),
            service('sylius.repository.order'),
            service('sylius_refund.validator.refund_units_command'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_refund.command_handler.generate_credit_memo', GenerateCreditMemoHandler::class)
        ->args([
            service('sylius_refund.generator.credit_memo'),
            service('sylius_refund.manager.credit_memo'),
            service('sylius.event_bus'),
            service('sylius.repository.order'),
            service('sylius_refund.resolver.credit_memo_file'),
            '%sylius_refund.pdf_generator.enabled%',
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_refund.command_handler.send_credit_memo', SendCreditMemoHandler::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.email_sender.credit_memo'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);
};
