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
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterUnitRefundAwareInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\DependencyInjection\SyliusRefundExtension;
use Sylius\RefundPlugin\Generator\RefundAllowedFilesOptionsProcessor;
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
            '@SyliusRefundPlugin/src/Migrations',
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


    /** @test */
    public function it_does_not_prepend_sylius_pdf_configuration_when_legacy_is_enabled(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_refund',
            ['pdf_generator' => ['legacy' => true]],
        );

        $this->prepend();

        $syliusPdfConfig = $this->container->getExtensionConfig('sylius_pdf_generation');

        self::assertEmpty($syliusPdfConfig);
    }

    /** @test */
    public function it_prepends_sylius_pdf_context_configuration_when_legacy_is_disabled(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_refund',
            ['pdf_generator' => ['legacy' => false]],
        );

        $this->prepend();

        $syliusPdfConfig = $this->container->getExtensionConfig('sylius_pdf_generation');

        self::assertNotEmpty($syliusPdfConfig);
        self::assertSame(
            [
                'contexts' => [
                    'sylius_refund' => [
                        'adapter' => 'knp_snappy',
                        'storage' => [
                            'type' => 'gaufrette',
                            'filesystem' => 'gaufrette.sylius_refund_credit_memo_filesystem',
                            'local_cache_directory' => '%kernel.cache_dir%/sylius_refund_pdf/',
                        ],
                    ],
                ],
            ],
            $syliusPdfConfig[0],
        );
    }

    /** @test */
    public function it_replaces_credit_memo_pdf_file_generator_argument_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        $definition = $this->container->getDefinition('sylius_refund.generator.credit_memo_pdf_file');

        self::assertEquals(
            TwigToPdfRendererInterface::class,
            (string) $definition->getArgument(4),
        );
    }

    /** @test */
    public function it_replaces_credit_memo_file_resolver_file_manager_argument_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        $definition = $this->container->getDefinition('sylius_refund.resolver.credit_memo_file');

        self::assertEquals(
            PdfFileManagerInterface::class,
            (string) $definition->getArgument(3),
        );
    }

    /** @test */
    public function it_replaces_credit_memo_file_path_resolver_argument_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        $definition = $this->container->getDefinition('sylius_refund.resolver.credit_memo_file_path');

        self::assertEquals(
            PdfFileManagerInterface::class,
            (string) $definition->getArgument(0),
        );
    }

    /** @test */
    public function it_does_not_replace_arguments_when_legacy_is_enabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => true]]);

        $generatorDefinition = $this->container->getDefinition('sylius_refund.generator.credit_memo_pdf_file');
        self::assertEquals(
            'sylius_refund.generator.twig_to_pdf',
            (string) $generatorDefinition->getArgument(4),
        );

        $resolverDefinition = $this->container->getDefinition('sylius_refund.resolver.credit_memo_file');
        self::assertEquals(
            'sylius_refund.manager.credit_memo_file',
            (string) $resolverDefinition->getArgument(3),
        );

        $pathResolverDefinition = $this->container->getDefinition('sylius_refund.resolver.credit_memo_file_path');
        self::assertEquals(
            '%sylius_refund.credit_memo_save_path%',
            (string) $pathResolverDefinition->getArgument(0),
        );
    }

    /** @test */
    public function it_registers_allowed_files_options_processor_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png'], 'legacy' => false]]);

        $this->assertContainerBuilderHasService(
            'sylius_refund.options_processor.knp_snappy.allowed_files',
            RefundAllowedFilesOptionsProcessor::class,
        );

        $definition = $this->container->getDefinition('sylius_refund.options_processor.knp_snappy.allowed_files');
        $tags = $definition->getTag('sylius_pdf_generation.options_processor');

        self::assertCount(1, $tags);
        self::assertSame('knp_snappy', $tags[0]['adapter']);
        self::assertSame('sylius_refund', $tags[0]['context']);
    }

    /** @test */
    public function it_does_not_register_allowed_files_options_processor_when_no_allowed_files(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        self::assertFalse($this->container->hasDefinition('sylius_refund.options_processor.knp_snappy.allowed_files'));
    }

    /** @test */
    public function it_does_not_register_allowed_files_options_processor_when_legacy_is_enabled(): void
    {
        $this->load(['pdf_generator' => ['allowed_files' => ['swans.png'], 'legacy' => true]]);

        self::assertFalse($this->container->hasDefinition('sylius_refund.options_processor.knp_snappy.allowed_files'));
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
