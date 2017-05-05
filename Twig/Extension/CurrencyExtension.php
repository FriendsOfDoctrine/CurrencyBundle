<?php

namespace Fod\CurrencyBundle\Twig\Extension;

use Fod\CurrencyBundle\Currency\Converter;
use Fod\CurrencyBundle\Currency\ConverterInterface;
use Fod\CurrencyBundle\Currency\FormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Construct.
     *
     * @param ContainerInterface $container We need the entire container to lazy load the Converter
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('currency_convert', [$this, 'convert']),
            new \Twig_SimpleFilter('currency_format', [$this, 'format']),
            new \Twig_SimpleFilter('currency_convert_format', [$this, 'convertAndFormat']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('currency_get_rate', [$this, 'getRate']),
            new \Twig_SimpleFunction('currency_get_diff_rate', [$this, 'getDiff']),
        ];
    }

    /**
     * @return Converter
     */
    public function getConverter()
    {
        return $this->container->get('fod_currency.converter');
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->container->get('fod_currency.formatter');
    }

    /**
     * Convert the given value.
     *
     * @param float $value
     * @param string $targetCurrency target currency code
     * @param boolean $round roud converted value
     * @param string $valueCurrency $value currency code
     *
     * @return float
     */
    public function convert($value, $targetCurrency, $round = true, $valueCurrency = null)
    {
        return $this->getConverter()->convert($value, $targetCurrency, $round, $valueCurrency);
    }

    /**
     * Format the given value.
     *
     * @param mixed $value
     * @param string $valueCurrency $value currency code
     * @param boolean $decimal show decimal part
     * @param boolean $symbol show currency symbol
     *
     * @return string
     */
    public function format($value, $valueCurrency = null, $decimal = true, $symbol = true)
    {
        if (null === $valueCurrency) {
            $valueCurrency = $this->getConverter()->getDefaultCurrency();
        }

        return $this->getFormatter()->format($value, $valueCurrency, $decimal, $symbol);
    }

    /**
     * Convert and format the given value.
     *
     * @param mixed $value
     * @param string $targetCurrency target currency code
     * @param boolean $decimal show decimal part
     * @param boolean $symbol show currency symbol
     * @param string $valueCurrency the $value currency code
     *
     * @return string
     */
    public function convertAndFormat($value, $targetCurrency, $decimal = true, $symbol = true, $valueCurrency = null)
    {
        $value = $this->convert($value, $targetCurrency, $decimal, $valueCurrency);

        return $this->format($value, $targetCurrency, $decimal, $symbol);
    }

    /**
     * @param string $targetCurrency
     * @param bool $withSymbol
     *
     * @return string
     */
    public function getRate(string $targetCurrency, $withSymbol = false)
    {
        return $this->format($this->getConverter()->convertCurrency($targetCurrency), $targetCurrency, true, $withSymbol);
    }

    /**
     * @param string $targetCurrency
     *
     * @return string
     */
    public function getDiff(string $targetCurrency)
    {
        $diffValue = $this->getConverter()->getConvertedDiffCurrency($targetCurrency);

        return ($diffValue >= 0 ? '+' : '-') . $this->format($diffValue, $targetCurrency, true, false);
    }
}
