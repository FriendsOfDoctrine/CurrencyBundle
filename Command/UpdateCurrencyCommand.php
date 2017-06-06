<?php

namespace Fod\CurrencyBundle\Command;

use Fod\CurrencyBundle\Adapter\AbstractCurrencyAdapter;
use Fod\CurrencyBundle\Currency\Currency;
use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCurrencyCommand
 * @package Fod\CurrencyBundle\Command
 */
class UpdateCurrencyCommand extends ContainerAwareCommand
{
    const CACHE_EXECUTE_FLAG = 'fod_currency_executed_update';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fod:currency:update')
            ->setDescription('Update currency rate')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Update currency force');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AbstractAdapter $cache */
        $cache = $this->getContainer()->get('cache.app');

        /** @var CacheItemInterface $executedCache */
        $executedCache = $cache->getItem(self::CACHE_EXECUTE_FLAG);

        if (!$executedCache->isHit() || $input->getOption('force')) {
            $cache->save($executedCache->set(time()));
            /** @var CacheItemInterface $currencyUpdateCache */
            $currencyUpdateCache = $cache->getItem(Currency::CACHE_KEY);

            if (!$currencyUpdateCache->isHit() || $input->getOption('force')) {
                /** @var AbstractCurrencyAdapter $adapter */
                $adapter = $this->getContainer()->get('fod_currency.adapter');
                $adapter->attachAll();

                $this->updateCache($cache, $adapter, Currency::CACHE_PREFIX_CURRENT);
                $cache->save($currencyUpdateCache->set(time())->expiresAfter($this->getContainer()->getParameter('fod_currency.cache_expired')));

                if (!$cache->getItem(Currency::CACHE_YESTERDAY_KEY)->isHit() || $input->getOption('force')) {
                    $this->updateCache($cache, $adapter, Currency::CACHE_PREFIX_YESTERDAY);
                    $cache->save($cache->getItem(Currency::CACHE_YESTERDAY_KEY)->set(time())->expiresAfter(86400));
                }
            }
            $cache->deleteItem($executedCache->getKey());
        } else {
            $output->writeln(sprintf('<info>Update process is running</info>'));
        }
    }

    /**
     * @param AbstractAdapter $cache
     * @param AbstractCurrencyAdapter $adapter
     * @param string $cachePrefix
     */
    protected function updateCache(AbstractAdapter $cache, AbstractCurrencyAdapter $adapter, string $cachePrefix)
    {
        /** @var Currency $currency */
        foreach ($adapter as $currency) {
            $cache->save($cache->getItem($cachePrefix . $currency->getCode())->set($currency->getRate()));
        }
    }
}
