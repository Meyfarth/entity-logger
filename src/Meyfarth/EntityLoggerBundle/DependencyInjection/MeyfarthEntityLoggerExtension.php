<?php

namespace Meyfarth\EntityLoggerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MeyfarthEntityLoggerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $this->loadEntityLoggerConfiguration($container, $loader, $config);
    }
    
    
    /**
     * 
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     * @param array $config
     * @todo treat config
     */
    private function loadEntityLoggerConfiguration(ContainerBuilder $container, LoaderInterface $loader, array $config){
        // Set up the entity logger listener
        $container->getDefinition('meyfarth.listener.entity_logger_listener')
                ->addMethodCall('setConfig', array($config));
        
        // If user logged, add mapping dynamically
    }
}
