<?php

namespace Fod\CurrencyBundle\Currency;

/**
 * Interface CurrencyInterface
 * @package Fod\CurrencyBundle\Currency
 */
interface CurrencyInterface
{
    public function getCode(): string;

    public function getRate(): float;

    public function setCode(string $code);

    public function setRate(float $rate);

    public function convert(float $rate);
}