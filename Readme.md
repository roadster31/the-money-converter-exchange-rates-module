# The Money Converter Exchange Rates

This module uses the The Money Converter currency exchange rates feed 
(http://themoneyconverter.com/EUR/Exchange_Rates_For_Euro.aspx), 
which provides more currencies than the default Thelia feed 
(http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml).

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is TheMoneyConverterExchangeRates.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require roadster31/the-money-converter-exchange-rates-module:~1.0
```