<?php

namespace Fod\CurrencyBundle\Adapter;

use Fod\CurrencyBundle\Currency\Currency;

class EcbCurrencyAdapter extends AbstractCurrencyAdapter
{
    /**
     * @var string
     */
    protected $ecbUrl = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * Set the ECB url.
     *
     * @param string $url
     */
    public function setEcbUrl($url)
    {
        $this->ecbUrl = $url;
    }

    /**
     * Init object storage
     */
    public function attachAll()
    {
        // Add euro
        /** @var Currency $euro */
        $euro = new $this->currencyClass;
        $euro->setCode('EUR');
        $euro->setRate(1);

        $this[$euro->getCode()] = $euro;

        // Get other currencies
        $xml = @simplexml_load_file($this->ecbUrl);

        if ($xml instanceof \SimpleXMLElement) {
            $data = $xml->xpath('//gesmes:Envelope/*[3]/*');

            foreach ($data[0]->children() as $child) {
                $code = (string)$child->attributes()->currency;

                if (in_array($code, $this->managedCurrencies)) {
                    /** @var Currency $currency */
                    $currency = new $this->currencyClass;
                    $currency->setCode($code);
                    $currency->setRate((string)$child->attributes()->rate);

                    $this[$currency->getCode()] = $currency;
                }
            }

            $this->afterAttachAll();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'ecb';
    }
}
