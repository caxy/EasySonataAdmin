<?php

namespace Caxy\EasySonataAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('easy_sonata_admin');

        $rootNode
            ->children()
            ->arrayNode('entities')
            ->prototype('array')
            ->children()
            ->scalarNode('class')->end()
            ->scalarNode('route_name')->end()
            ->scalarNode('route_pattern')->end()
            ->variableNode('batch_actions')->end()
            ->variableNode('list')->end()
            ->variableNode('edit')->end()
            ->variableNode('show')->end()
            ->variableNode('filter')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
