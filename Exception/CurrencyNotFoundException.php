<?php

namespace Fod\CurrencyBundle\Exception;

class CurrencyNotFoundException extends \InvalidArgumentException
{
    public function __construct($currency)
    {
        parent::__construct(sprintf('Can\'t find currency: "%s"', $currency));
    }
}