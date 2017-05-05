<?php

namespace Fod\CurrencyBundle\Adapter;

use Fod\CurrencyBundle\Currency\Currency;
use Fod\CurrencyBundle\Exception\CurrencyNotFoundException;

class YahooCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    protected $yahooUrl = 'https://query.yahooapis.com/v1/public/yql';

    /**
     * @var array
     */
    protected $currencyCodes = [];


    /**
     * Set the Yahoo! url.
     *
     * @param string $url
     */
    public function setYahooUrl($url)
    {
        $this->yahooUrl = $url;
    }

    /**
     * Init object storage
     */
    public function attachAll()
    {
        foreach ($this->managedCurrencies as $managedCurrency) {
            $this->addCurrency($managedCurrency);
        }

        // Add default currency (euro in this example)
        /** @var Currency $euro */
        $euro = new $this->currencyClass;
        $euro->setCode('EUR');
        $euro->setRate(1);

        $this[$euro->getCode()] = $euro;

        // Build YQL query
        $strCodes = '';
        foreach ($this->currencyCodes as $index => $currencyCode) {
            $strCodes .= "'EUR" . $currencyCode . "'";
            if ($index != count($this->currencyCodes) - 1) {
                $strCodes .= ", ";
            }
        }

        $yqlQuery = "select id,Rate from yahoo.finance.xchange where pair in (" . $strCodes . ")";

        $yqlQueryURL = $this->yahooUrl
            . "?q=" . urlencode($yqlQuery)
            . "&format=json"
            . "&env=store://datatables.org/alltableswithkeys";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $yqlQueryURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $json = curl_exec($ch);

        // Convert JSON response to PHP object
        $data = json_decode($json);
        $results = $data->query->results->rate;

        // Check if query was okay and result is given
        if (null === $results) {
            new \RuntimeException('YQL query failed!');
        }

        $currencies = [];

        foreach ($results as $row) {
            $code = substr($row->id, 3);
            $rate = $row->Rate;

            $currencies[$code] = $rate;
        }

        foreach ($currencies as $code => $rate) {
            if (in_array($code, $this->managedCurrencies)) { // you can check if the currency is in the managed currencies
                /** @var Currency $currency */
                $currency = new $this->currencyClass;
                $currency->setCode($code);
                $currency->setRate($rate);

                $this[$currency->getCode()] = $currency;
            }
        }

        $this->afterAttachAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'yahoo';
    }

    /**
     * Add currency to the query
     *
     * @param $code
     */
    private function addCurrency($code)
    {
        $this->currencyCodes[] = $code;
    }
}