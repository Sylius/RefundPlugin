<?php

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator, ContainerBuilder $container): void {
    $container->setParameter('sylius_refund.is_sylius_greater_equal_than_21', SyliusCoreBundle::VERSION_ID >= 20100);
    $container->setParameter('sylius_refund.is_sylius_lower_than_21', SyliusCoreBundle::VERSION_ID < 20100);
};
