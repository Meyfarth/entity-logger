<?php

namespace Meyfarth\EntityLoggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * @todo config
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('meyfarth_entity_logger');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        
        $rootNode
            ->info('Activate the EntityLogger component (used to log all entity modification)')
            ->children()
                ->booleanNode('enable')
                    ->defaultFalse()
                ->end()
                ->arrayNode('log')
                    ->info('Customize which action will be logged')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('create')
                            ->info('Set true (default) if you want to log entities when they are created.')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('update')
                            ->info('Set true (default) if you want to log entities when they are updated.')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('delete')
                            ->info('Set true (default) if you want to log entities when they are deleted.')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('log_user')
                    ->info('Log user managed by security.context')
                    ->defaultFalse()
                ->end()
                ->enumNode('log_type')
                    ->info('Set to original_data (default) to log the original data (before it is saved into the database). Set to modified_data to log the modified_data (after beeing saved in database).')
                    ->values(array('original_data', 'modified_data'))
                    ->defaultValue('original_data')
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
