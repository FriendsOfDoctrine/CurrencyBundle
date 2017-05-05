<?php

namespace Fod\CurrencyBundle\Currency;

/**
 * Interface CurrencyDiffInterface
 * @package Fod\CurrencyBundle\Currency
 */
interface CurrencyDiffInterface
{
    public function getYesterdayRate(): float;

    public function setYesterdayRate(float $rate);

    public function getDiff(): float;
}