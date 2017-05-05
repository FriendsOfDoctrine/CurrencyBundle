<?php

namespace Fod\CurrencyBundle\Adapter;

use Fod\CurrencyBundle\Currency\Currency;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class CurrencyAdapterFactory
 * @package Fod\CurrencyBundle\Adapter
 */
class CurrencyAdapterFactory
{
    /**
     * @var array
     */
    protected static $currencies = [];

    /**
     * CurrencyAdapterFactory constructor.
     *
     * @param $defaultCurrency
     * @param $availableCurrencies
     */
    public function __construct($defaultCurrency, $availableCurrencies)
    {
        self::$currencies['default'] = $defaultCurrency;
        self::$currencies['managed'] = $availableCurrencies;
    }

    /**
     * @param string $class
     * @param AbstractAdapter $cache
     * @param ContainerInterface $container
     *
     * @return AbstractCurrencyAdapter
     */
    public static function createCurrencyAdapter(string $class, AbstractAdapter $cache, ContainerInterface $container)
    {
        /** @var AbstractCurrencyAdapter $currencyAdapter */
        $currencyAdapter = new $class;

        if (!$currencyAdapter instanceof AbstractCurrencyAdapter) {
            throw new \InvalidArgumentException('Class \'' . $class . '\' it is not instance of AbstractCurrencyAdapter');
        }

        $currencyAdapter->setDefaultCurrency(self::$currencies['default']);
        $currencyAdapter->setManagedCurrencies(self::$currencies['managed']);

        $currencyCache = $cache->getItem(Currency::CACHE_KEY);

        if (!$currencyCache->isHit()) {
            self::startUpdateProcess($container->getParameter('fod_currency.update_command'), $container->getParameter('kernel.root_dir'));
        }

        foreach ($currencyAdapter->getManagedCurrencies() as $currencyCode) {
            $currencyAdapter->add(new Currency($currencyCode, (float)$cache->getItem(Currency::CACHE_PREFIX_CURRENT . $currencyCode)->get(), (float)$cache->getItem(Currency::CACHE_PREFIX_YESTERDAY . $currencyCode)->get()));
        }

        return $currencyAdapter->afterAttachAll();
    }

    /**
     * @param string $updateCommand
     * @param string $rootDir
     */
    protected static function startUpdateProcess(string $updateCommand, string $rootDir)
    {
        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix('php')
            ->setArguments([$rootDir . '/../bin/console', $updateCommand])
            ->getProcess();

        $process->start();
    }
}