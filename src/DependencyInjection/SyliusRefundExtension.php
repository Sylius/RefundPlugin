<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class SyliusRefundExtension extends AbstractResourceExtension implements CompilerPassInterface
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius_refund', 'doctrine/orm', $config['resources'], $container);

        $loader->load('services.xml');
    }

    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('sylius_refund.factory.refund_payment')
            ->addArgument(new Reference('sylius.repository.payment_method'));
    }
}
