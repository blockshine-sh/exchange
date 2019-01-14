<?php

namespace ExchangeCenter\Exchanges\Rightbtc;

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
    protected $exchange = 'rightbtc';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        if (empty($this->data['result'])) {
            return [];
        }
        return $this->convertData();
    }

    /**
     * @author liyu
     * @return array
     */
    private function getPairsMap()
    {
        $pairsMap = [];
        $pairs = (new Pairs())->getData();

        foreach ($pairs as $pair) {
            $digitalCurrency = $pair['digital_currency'];
            $marketCurrency = $pair['market_currency'];
            $symbol = "{$digitalCurrency}{$marketCurrency}";
            $pairsMap[$symbol] = $pair;
        }
        return $pairsMap;
    }

    private function convertData()
    {

        $pairsMap = $this->getPairsMap();

        $ticker_data = [];
        foreach ($this->data['result'] as $item) {

            $symbol = strtoupper($item['market']);

            if (!isset($pairsMap[$symbol])) {
                //echo $this->exchange."[$symbol]异常\n";
                continue;
            }
            $pair = $pairsMap[$symbol];
            $digitalCurrency = $pair['digital_currency'];
            $marketCurrency = $pair['market_currency'];

            $timestamp = substr($item['date'], 0, 10);

            $ticker = new TickerModel();
            $ticker->digital_currency       = $digitalCurrency;
            $ticker->market_currency        = $marketCurrency;

            $ticker->open                   = null;
            $ticker->high                   = $item['high'] / pow(10, 8);
            $ticker->low                    = $item['low'] / pow(10, 8);
            $ticker->close                  = $item['last'] / pow(10, 8);
            $ticker->amount                 = $item['vol24h'] / pow(10, 8);
            $ticker->vol                    = $ticker->amount * $ticker->close;
            $ticker->timestamp              = $timestamp;
            //$ticker->price_pcnt             = $ticker->close - $ticker->open;
            $ticker->bid1                   = $item['buy'] / pow(10, 8);
            $ticker->ask1                   = $item['sell'] / pow(10, 8);

            $ticker_data["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }
}