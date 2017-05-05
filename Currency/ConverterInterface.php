<?php

namespace Fod\CurrencyBundle\Currency;

interface ConverterInterface
{
    /**
     * Convert from default currency to another.
     *
     * @param float $value
     * @param string $targetCurrency
     * @param boolean $round
     * @param string $valueCurrency
     *
     * @return float
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null);

    /**
     * Get default currency.
     *
     * @return string
     */
    public function getDefaultCurrency();
}
