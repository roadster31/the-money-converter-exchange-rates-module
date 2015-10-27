<?php
/*************************************************************************************/
/*                                                                                   */
/*      This file is not free software                                               */
/*                                                                                   */
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 27/10/2015 10:06
 */

namespace TheMoneyConverterExchangeRates\CurrencyConverterProvider;

use Thelia\CurrencyConverter\Exception\CurrencyNotFoundException;
use Thelia\CurrencyConverter\Provider\BaseProvider;
use Thelia\CurrencyConverter\Provider\ProviderInterface;
use Thelia\Math\Number;

class TMCProvider extends BaseProvider implements ProviderInterface
{
    protected $endPoint = "http://themoneyconverter.com/rss-feed/EUR/rss.xml";

    protected $data;

    public function __construct($loadWebService = true)
    {
        if (true === $loadWebService) {
            $this->loadFromWebService();
        }
    }


    private function loadFromWebService()
    {
        $feed = new \SimplePie();

        $feed->set_feed_url($this->endPoint);
        $feed->set_cache_location(THELIA_ROOT . 'cache/feeds');

        $feed->init();

        $feed->handle_content_type();

        $feed->set_timeout(10);

        $this->data = $feed->get_items();
    }

    public function convert(Number $number)
    {
        $rateFactor = $this->retrieveRateFactor();

        if ($this->to === 'EUR') {
            return $number->multiply($rateFactor);
        } else {
            return $this->convertToOther($rateFactor, $number);
        }
    }

    /**
     * @param \Thelia\Math\Number $rateFactor
     * @param \Thelia\Math\Number $number
     * @return \Thelia\Math\Number
     * @throws \Thelia\CurrencyConverter\Exception\CurrencyNotFoundException if the `to` currency is not support
     */
    private function convertToOther(Number $rateFactor, Number $number)
    {
        $rateStr = $this->getRateFromFeed($this->to);

        $rate = $rateFactor->multiply($rateStr);

        return $number->multiply($rate);
    }

    /**
     * @return \Thelia\Math\Number
     * @throws \Thelia\CurrencyConverter\Exception\CurrencyNotFoundException if the `from` currency is not support
     */
    private function retrieveRateFactor()
    {
        if ($this->from === 'EUR') {
            return new Number(1);
        }

        $rateStr = $this->getRateFromFeed($this->from);

        $rate = new Number($rateStr);
        $base = new Number(1);

        return $base->divide($rate);
    }

    private function getRateFromFeed($code)
    {
        $searchedTitle = $code . '/EUR';

        /** @var \SimplePie_Item $item */
        foreach ($this->data as $item) {
            if ($searchedTitle == $item->get_title()) {
                if (1 === sscanf($item->get_description(), "1 Euro = %f", $rateStr)) {
                    return $rateStr;
                }
            }
        }

        throw new CurrencyNotFoundException($code);
    }
}
