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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_refund_plugin');
        $rootNode = $treeBuilder->getRootNode();

        $this->addPdfGeneratorSection($rootNode);

        return $treeBuilder;
    }

    private function addPdfGeneratorSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('pdf_generator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('allowed_files')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
