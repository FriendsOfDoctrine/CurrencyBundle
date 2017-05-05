<?php

namespace Fod\CurrencyBundle\Currency;

/**
 * Class Currency
 * @package Fod\CurrencyBundle\Currency
 */
class Currency implements CurrencyInterface, CurrencyDiffInterface
{
    const EUR = 'EUR';
    const USD = 'USD';
    const RUB = 'RUB';

    const CACHE_KEY = 'fod_currency_cache';
    const CACHE_YESTERDAY_KEY = 'fod_currency_yesterday_cache';
    const CACHE_PREFIX_CURRENT = 'cache_fod_currency_today_';
    const CACHE_PREFIX_YESTERDAY = 'cache_fod_currency_yesterday_';

    /** @var  string */
    protected $code;
    /** @var  float */
    protected $rate;
    /** @var  float */
    protected $yesterdayRate;

    /**
     * Currency constructor.
     *
     * @param string $code
     * @param float $rate
     * @param float $yesterdayRate
     */
    public function __construct(string $code = 'EUR', float $rate = 0.0, float $yesterdayRate = 0.0)
    {
        $this->code = $code;
        $this->rate = $rate;
        $this->yesterdayRate = $yesterdayRate;
    }

    /**
     * @return array
     */
    public static function getCodes()
    {
        return [
            self::EUR,
            self::USD,
            self::RUB,
        ];
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @param float $rate
     */
    public function setRate(float $rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * Convert currency rate
     *
     * @param float $rate
     */
    public function convert(float $rate)
    {
        if ((int)$rate !== 0) {
            $this->rate /= $rate;
        }
    }

    /**
     * @return float
     */
    public function getYesterdayRate(): float
    {
        return $this->yesterdayRate;
    }

    /**
     * @param float $rate
     */
    public function setYesterdayRate(float $rate)
    {
        $this->yesterdayRate = $rate;
    }

    /**
     * @return float
     */
    public function getDiff(): float
    {
        return $this->getRate() - $this->getYesterdayRate();
    }
}