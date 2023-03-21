<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterUnitRefundAwareInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\DependencyInjection\SyliusRefundExtension;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class SyliusRefundExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_prepending_doctrine_migration_with_proper_migrations_paths(): void
    {
        $this->configureContainer();

        $this->load();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertTrue(isset(
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\RefundPlugin\Migrations']
        ));
        self::assertSame(
            '@SyliusRefundPlugin/Migrations',
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\RefundPlugin\Migrations']
        );

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        self::assertTrue(isset(
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\RefundPlugin\Migrations']
        ));
        self::assertSame(
            'Sylius\Bundle\CoreBundle\Migrations',
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\RefundPlugin\Migrations'][0]
        );
    }

    /** @test */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled(): void
    {
        $this->configureContainer();

        $this->container->setParameter('sylius_core.prepend_doctrine_migrations', false);

        $this->load();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertEmpty($doctrineMigrationsExtensionConfig);

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        self::assertEmpty($syliusLabsDoctrineMigrationsExtraExtensionConfig);
    }

    /** @test */
    public function it_prepends_configuration_with_enabled_pdf_generator(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_refund',
            ['pdf_generator' => ['enabled' => false]]
        );

        $this->prepend();

        $this->assertContainerBuilderHasParameter('sylius_refund.pdf_generator.enabled', false);
    }

    /** @test */
    public function it_prepends_configuration_with_enabled_pdf_generator_parameter_by_default_as_true(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter('sylius_refund.pdf_generator.enabled', true);
    }

    /** @test */
    public function it_sets_up_the_pdf_generator_allow_files_container_parameter_by_default_as_an_empty_array(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_refund.pdf_generator.allowed_files', []);
    }

    /** @test */
    public function it_adds_tags_to_autoconfigurable_interfaces(): void
    {
        $this->load();

        $autoconfigurableInterfaces = $this->container->getAutoconfiguredInstanceof();

        $expectedTaggedInterfaces = [
            'sylius_refund.units_refunded.process_step' => UnitsRefundedProcessStepInterface::class,
            'sylius_refund.line_item_converter' => LineItemsConverterUnitRefundAwareInterface::class,
            'sylius_refund.request_to_refund_units_converter' => RequestToRefundUnitsConverterInterface::class,
            'sylius_refund.refunder' => RefunderInterface::class,
            'sylius_refund.refund_unit_total_provider' => RefundUnitTotalProviderInterface::class,
        ];

        foreach ($expectedTaggedInterfaces as $tag => $interface) {
            $this->assertArrayHasKey($interface, $autoconfigurableInterfaces);
            $this->assertTrue($autoconfigurableInterfaces[$interface]->hasTag($tag));
        }
    }


    protected function getContainerExtensions(): array
    {
        return [new SyliusRefundExtension()];
    }

    private function configureContainer(): void
    {
        $this->container->setParameter('kernel.environment', 'test');
        $this->container->setParameter('kernel.debug', true);

        $this->container->registerExtension(new DoctrineMigrationsExtension());
        $this->container->registerExtension(new SyliusLabsDoctrineMigrationsExtraExtension());
    }

    private function prepend(): void
    {
        foreach ($this->container->getExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($this->container);
            }
        }
    }
}
