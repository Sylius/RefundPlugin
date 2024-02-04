<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterUnitRefundAwareInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusRefundExtension extends Extension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $configs = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->tagsAutoconfiguration($container, [
            'sylius_refund.units_refunded.process_step' => UnitsRefundedProcessStepInterface::class,
            'sylius_refund.line_item_converter' => LineItemsConverterUnitRefundAwareInterface::class,
            'sylius_refund.request_to_refund_units_converter' => RequestToRefundUnitsConverterInterface::class,
            'sylius_refund.refunder' => RefunderInterface::class,
            'sylius_refund.refund_unit_total_provider' => RefundUnitTotalProviderInterface::class,
        ]);

        $container->setParameter('sylius_refund.pdf_generator.allowed_files', $configs['pdf_generator']['allowed_files']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $this->getCurrentConfiguration($container);

        $container->setParameter('sylius_refund.pdf_generator.enabled', $configs['pdf_generator']['enabled']);

        $this->prependDoctrineMigrations($container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Sylius\RefundPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusRefundPlugin/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }

    private function getCurrentConfiguration(ContainerBuilder $container): array
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $configs = $container->getExtensionConfig($this->getAlias());

        return $this->processConfiguration($configuration, $configs);
    }

    /** @param array<string, class-string> $taggedInterfaces */
    private function tagsAutoconfiguration(ContainerBuilder $container, array $taggedInterfaces): void
    {
        foreach ($taggedInterfaces as $tag => $interface) {
            $container->registerForAutoconfiguration($interface)->addTag($tag);
        }
    }
}
