<?php

namespace Fod\CurrencyBundle\Currency;

use Fod\CurrencyBundle\Adapter\AbstractCurrencyAdapter;
use Fod\CurrencyBundle\Exception\CurrencyNotFoundException;

class Converter implements ConverterInterface
{
    const MODE_UP = 'up';
    const MODE_DOWN = 'down';
    const MODE_EVEN = 'even';
    const MODE_ODD = 'odd';

    /**
     * @var AbstractCurrencyAdapter
     */
    protected $adapter;

    /**
     * @var integer
     */
    protected $precision;

    /**
     * @var string
     */
    protected $roundMode;

    /**
     * Construct.
     *
     * @param AbstractCurrencyAdapter $adapter
     * @param integer $precision
     * @param string $roundMode
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(AbstractCurrencyAdapter $adapter, $precision = 2, $roundMode = 'up')
    {
        if (!in_array($roundMode, self::getAllowedModes(), true)) {
            throw new \InvalidArgumentException(sprintf('Invalid round mode "%s", please use one of the following values: %s', $roundMode, implode(', ', self::getAllowedModes())));
        }

        $this->adapter = $adapter;
        $this->precision = $precision;
        $this->roundMode = constant(sprintf('PHP_ROUND_HALF_%s', strtoupper($roundMode)));
    }

    /**
     * @return array
     */
    public static function getAllowedModes()
    {
        return [
            self::MODE_UP,
            self::MODE_DOWN,
            self::MODE_EVEN,
            self::MODE_ODD,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        if (!isset($this->adapter[$targetCurrency])) {
            throw new CurrencyNotFoundException($targetCurrency);
        }

        if (null === $valueCurrency) {
            $valueCurrency = $this->getDefaultCurrency();
        }

        if (!isset($this->adapter[$valueCurrency])) {
            throw new CurrencyNotFoundException($valueCurrency);
        }

        if ($targetCurrency !== $valueCurrency) {
            if ($this->getDefaultCurrency() === $valueCurrency) {
                $value *= $this->adapter[$targetCurrency]->getRate();

            } else {
                $value /= $this->adapter[$valueCurrency]->getRate();

                if ($this->getDefaultCurrency() !== $targetCurrency) {
                    $value *= $this->adapter[$targetCurrency]->getRate();
                }
            }
        }

        return $round ? round($value, $this->precision, $this->roundMode) : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function convertYesterday($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        if (!isset($this->adapter[$targetCurrency])) {
            throw new CurrencyNotFoundException($targetCurrency);
        }

        if (null === $valueCurrency) {
            $valueCurrency = $this->getDefaultCurrency();
        }

        if (!isset($this->adapter[$valueCurrency])) {
            throw new CurrencyNotFoundException($valueCurrency);
        }

        if ($targetCurrency !== $valueCurrency) {
            if ($this->getDefaultCurrency() === $valueCurrency) {
                $value *= $this->adapter[$targetCurrency]->getYesterdayRate();

            } else {
                $value /= $this->adapter[$valueCurrency]->getYesterdayRate();

                if ($this->getDefaultCurrency() !== $targetCurrency) {
                    $value *= $this->adapter[$targetCurrency]->getYesterdayRate();
                }
            }
        }

        return $round ? round($value, $this->precision, $this->roundMode) : $value;
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     *
     * @return float
     */
    public function convertCurrency(string $fromCurrency, string $toCurrency = null)
    {
        return $this->convert(1, $toCurrency ?: $this->getDefaultCurrency(), true, $fromCurrency);
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     *
     * @return float
     */
    public function convertYesterdayCurrency(string $fromCurrency, string $toCurrency = null)
    {
        return $this->convertYesterday(1, $toCurrency ?: $this->getDefaultCurrency(), true, $fromCurrency);
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     *
     * @return float
     */
    public function getConvertedDiffCurrency(string $fromCurrency, string $toCurrency = null)
    {
        return (float)($this->convertCurrency($fromCurrency, $toCurrency) - $this->convertYesterdayCurrency($fromCurrency, $toCurrency));
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultCurrency()
    {
        return $this->adapter->getDefaultCurrency();
    }

    /**
     * @param string $currency
     * @return Currency|null
     */
    public function getCurrency(string $currency)
    {
        return $this->adapter[$currency]??null;
    }
}
