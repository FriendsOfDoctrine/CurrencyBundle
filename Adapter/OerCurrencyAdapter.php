<?php

namespace Fod\CurrencyBundle\Adapter;

use Fod\CurrencyBundle\Currency\Currency;
use Fod\CurrencyBundle\Exception\CurrencyNotFoundException;

class OerCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    protected $url = 'http://openexchangerates.org/api/latest.json';

    /**
     * @var string
     */
    protected $appId;

    /**
     * Set the OER url.
     *
     * @param string $url
     */
    public function setOerUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Sets the app-id
     *
     * @param string $appId
     */
    public function setOerAppId($appId)
    {
        $this->appId = $appId;
    }


    /**
     * Init object storage
     */
    public function attachAll()
    {
        // Get other currencies
        $data = @file_get_contents($this->getUrl());
        $data = @json_decode($data, true);

        if ($data && is_array($data) && isset($data['rates'])) {
            $data = $data['rates'];
            foreach ($this->managedCurrencies as $code) {
                if (isset($data[$code])) {
                    /** @var Currency $currency */
                    $currency = new $this->currencyClass;
                    $currency->setCode($code);
                    $currency->setRate($data[$code]);

                    $this[$code] = $currency;
                }
            }

            $this->afterAttachAll();
        }
    }

    public function getUrl()
    {
        if (!$this->appId)
            throw new \InvalidArgumentException('OER_APP_ID must be set in order to use OerCurrencyAdapter');
        return sprintf("%s?app_id=%s", $this->url, $this->appId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'oer';
    }
}