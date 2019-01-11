<?php

namespace ExchangeCenter\Exchanges\Cointiger;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:34
 */
class Ticker extends ExchangeBase
{
    protected $exchange = 'cointiger';

    public function getData($options = [])
    {
        $pairs = (new Pairs($this->exchange))->getData();
        if (empty($pairs)) {
            Helper::fail('获取交易对失败');
        }

        $market_currencies = array_map(function ($val) {
            return strtolower($val);
        }, array_unique(array_column($pairs, 'market_currency')));

        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        if (empty($this->data)) {
            return [];
        }
        return $this->convertData($market_currencies);

    }

    private function convertData($market_currencies)
    {
        $tickers = [];
        if (!empty($this->data)) {
            foreach ($this->data as $pair => $datum) {
                foreach ($market_currencies as $market_currency) {
                    $_market_currenty = substr($pair, -strlen($market_currency));
                    if ($_market_currenty == strtoupper($market_currency)) {
                        $_digital_currency = substr($pair, 0, -strlen($market_currency));
                        $ticker = new TickerModel();
                        $ticker->digital_currency = $_digital_currency;
                        $ticker->market_currency = $_market_currenty;
                        $ticker->open = bcmul($datum['last'], 1-$datum['percentChange'], 8);
                        $ticker->high = $datum['high24hr'];
                        $ticker->low = $datum['low24hr'];
                        $ticker->close = $datum['last'];
                        $ticker->amount = $datum['baseVolume'];
                        $ticker->vol = $datum['quoteVolume'];
                        $ticker->bid1 = $datum['highestBid'];
                        $ticker->ask1 = $datum['lowestAsk'];
                        $ticker->timestamp = floor($datum['id'] / 1000);
                        $tickers[$pair] = Helper::toArray($ticker);
                        break;
                    }
                }
            }
        }
        return $tickers;
    }

    private function handleError()
    {

    }
}