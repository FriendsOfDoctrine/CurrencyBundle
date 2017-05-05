<?php

namespace Fod\CurrencyBundle\Adapter;

use Fod\CurrencyBundle\Currency\Currency;

abstract class AbstractCurrencyAdapter extends \ArrayIterator
{
    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * @var array
     */
    protected $managedCurrencies = [];

    /**
     * @var string
     */
    protected $currencyClass = Currency::class;

    /**
     * Set default currency
     *
     * @param string $defaultCurrency
     */
    public function setDefaultCurrency($defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * Get default currency
     *
     * @return string
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * Get managedCurrencies
     *
     * @param array $currencies
     */
    public function setManagedCurrencies($currencies)
    {
        $this->managedCurrencies = $currencies;
    }

    /**
     * Get managedCurrencies
     *
     * @return array
     */
    public function getManagedCurrencies()
    {
        return $this->managedCurrencies;
    }

    /**
     * Get managedCurrencies
     *
     * @return array
     */
    public function setCurrencyClass($currencyClass)
    {
        return $this->currencyClass = $currencyClass;
    }

    /**
     * Set object
     *
     * @param mixed $index
     * @param Currency $newval
     */
    public function offsetSet($index, $newval)
    {
        if (!$newval instanceof $this->currencyClass) {
            throw new \InvalidArgumentException(sprintf('$newval must be an instance of Currency, instance of "%s" given', get_class($newval)));
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * Append a value
     *
     * @param Currency $value
     */
    public function append($value)
    {
        if (!$value instanceof $this->currencyClass) {
            throw new \InvalidArgumentException(sprintf('$newval must be an instance of Currency, instance of "%s" given', get_class($value)));
        }

        parent::append($value);
    }

    /**
     * @param Currency $currency
     */
    public function add(Currency $currency)
    {
        $this[$currency->getCode()] = $currency;
    }

    /**
     * @return $this
     */
    public function afterAttachAll()
    {
        $this->convertAll(isset($this[$this->defaultCurrency]) ? $this[$this->defaultCurrency]->getRate() : 1);

        return $this;
    }

    /**
     * Convert all
     *
     * @param mixed $rate
     */
    protected function convertAll($rate)
    {
        foreach ($this as $currency) {
            $currency->convert($rate);
        }
    }

    /**
     * This method is used by the constructor
     * to attach all currencies.
     */
    abstract public function attachAll();

    /**
     * Get identier value for the adapter must be unique
     * for all the project
     *
     * @return string
     */
    abstract public function getIdentifier();
}