<?php

namespace Fod\CurrencyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fod_currency');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('currencies')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')
                            ->cannotBeEmpty()
                            ->defaultValue('EUR')
                            ->isRequired()
                        ->end()
                        ->arrayNode('managed')
                            ->defaultValue(['EUR'])
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('decimal_part')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('precision')
                            ->defaultValue(2)
                        ->end()
                        ->scalarNode('round_mode')
                            ->defaultValue('up')
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('adapter_class')
                    ->cannotBeEmpty()
                    ->defaultValue('Fod\CurrencyBundle\Adapter\EcbCurrencyAdapter')
                ->end()

                ->scalarNode('update_command')
                    ->cannotBeEmpty()
                    ->defaultValue('fod:currency:update')
                ->end()

                ->scalarNode('cache_expired')
                    ->cannotBeEmpty()
                    ->defaultValue(14400)
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
