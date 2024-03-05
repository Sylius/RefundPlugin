<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator, ContainerBuilder $container): void {
    if (class_exists('\Symfony\Component\Workflow\Workflow')) {
        $configurator->import('../integrations/workflow.yaml');
    }
};
