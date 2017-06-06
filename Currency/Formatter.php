<?php

namespace Fod\CurrencyBundle\Currency;


class Formatter implements FormatterInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $cleanCharacters;

    /**
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
        $this->cleanCharacters = ['EU', 'UK', 'US'];
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $valueCurrency = null, $decimal = true, $symbol = true)
    {
        $formatter = new \NumberFormatter($this->locale, $symbol ? \NumberFormatter::CURRENCY : \NumberFormatter::PATTERN_DECIMAL);
        $value = $formatter->formatCurrency($value, $valueCurrency);
        if (strpos($value, ',') === 0) {
            $value = '0' . $value;
        }
        if (!$decimal) {
            $value = preg_replace('/[.,]00((?=\D)|$)/', '', $value);
        }

        if (count($this->cleanCharacters) > 0) {
            $value = str_replace($this->cleanCharacters, '', $value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setLocale($locale)
    {
        $this->locale = (string)$locale;
    }

    /**
     * @param array $cleanCharacters
     */
    public function setCleanCharacters(array $cleanCharacters)
    {
        $this->cleanCharacters = $cleanCharacters;
    }
}
