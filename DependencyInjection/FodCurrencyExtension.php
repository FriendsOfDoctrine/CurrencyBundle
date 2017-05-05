<?php

namespace Fod\CurrencyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class FodCurrencyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('fod_currency.currencies.default', $config['currencies']['default']);
        $container->setParameter('fod_currency.currencies.managed', $config['currencies']['managed']);

        $container->setParameter('fod_currency.decimal_part.precision', $config['decimal_part']['precision']);
        $container->setParameter('fod_currency.decimal_part.round_mode', $config['decimal_part']['round_mode']);

        // Add default currency to managed currencies if needed
        if (!in_array($config['currencies']['default'], $config['currencies']['managed'])) {
            $config['currencies']['managed'][] = $config['currencies']['default'];
        }

        $container->setParameter('fod_currency.update_command', $config['update_command']);
        $container->setParameter('fod_currency.adapter_class', $config['adapter_class']);

        $container->setParameter('fod_currency.cache_expired', $config['cache_expired']);
    }
}
