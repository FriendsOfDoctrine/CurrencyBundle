<?php

namespace Fod\CurrencyBundle\Command;

use Fod\CurrencyBundle\Currency\Currency;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setDescription('Update currency rate');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = $this->getContainer()->get('cache.app');

        $executedCache = $cache->getItem(self::CACHE_EXECUTE_FLAG);

        if (!$executedCache->isHit()) {
            $cache->save($executedCache->set(time()));
            $currencyUpdateCache = $cache->getItem(Currency::CACHE_KEY);

            if (!$currencyUpdateCache->isHit()) {
                $adapter = $this->getContainer()->get('fod_currency.adapter');
                $adapter->attachAll();
                /** @var Currency $currency */
                foreach ($adapter as $currency) {
                    $currencyCache = $cache->getItem(Currency::CACHE_PREFIX_CURRENT . $currency->getCode());
                    if (!$currencyCache->isHit()) {
                        $output->writeln(sprintf('<comment>Add: %s = %s</comment>', $currency->getCode(), $currency->getRate()));
                    } else {
                        $output->writeln(sprintf('<comment>Update: %s = %s</comment>', $currency->getCode(), $currency->getRate()));
                    }
                    $currencyCache->set($currency->getRate());
                    $cache->save($currencyCache);

                    $currencyCache = $cache->getItem(Currency::CACHE_PREFIX_YESTERDAY . $currency->getCode());
                    if (!$currencyCache->isHit()) {
                        $currencyCache->set($currency->getRate())->expiresAfter(86400);
                        $cache->save($currencyCache);
                    }
                }

                $currencyUpdateCache->set(time());
                $currencyUpdateCache->expiresAfter($this->getContainer()->getParameter('fod_currency.cache_expired'));

                $cache->save($currencyUpdateCache);
            }
            $cache->deleteItem($executedCache->getKey());
        } else {
            $output->writeln(sprintf('<info>Update process is running</info>'));
        }
    }
}
