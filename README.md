Overview
========

This Symfony3.2 bundle provide a service and a twig extension to convert and display currencies.
based on [LexicCurrencyBundle](https://github.com/lexik/LexikCurrencyBundle)

Installation
============

Register the bundle with your kernel:

```
// in AppKernel::registerBundles()
$bundles = [
    // ...
    new Fod\CurrencyBundle\FodCurrencyBundle(),
    // ...
];
```

Configuration
=============

Minimum configuration:

```yaml
# app/config/config.yml
fod_currency:
    currencies:
        default: RUB              # [required] the default currency
        managed: [RUB, EUR, USD]  # [required] all currencies used in your app
```

Addition options (default values are shown here):

```yaml
# app/config/config.yml
fod_currency:
    decimal_part:
        precision:  2                           # number of digits for the decimal part
        round_mode: up                          # round mode to use (up|down|even|odd)
    adapter_class: Fod\CurrencyBundle\Adapter\EcbCurrencyAdapter  # Use your custom Currency Adapter
    update_command: fod:currency:update         # name of console command for update currencies
    cache_expired: 14400                        # currency cached time
```

Usage
=====

##### Twig functions

The bundle provide 2 functions for display currencies:
* `currency_get_rate`: display current rate value of currency
* `currency_get_diff_rate`: display diff between current and yesterday rate value of currency

Here an example `currency_get_rate` function.

```
{{ currency_get_rate('USD') }}
```

Here an example `currency_get_diff_rate` function.

```
{{ currency_get_diff_rate('EUR') }}
```