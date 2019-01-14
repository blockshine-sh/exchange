<?php

namespace ExchangeCenter\Exchanges\Huobi;

use ExchangeCenter\Exchange;
use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;
use ExchangeCenter\Exchanges\Huobi\Pairs;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:34
 */
class Ticker extends ExchangeBase
{
    protected $exchange = 'huobi';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        if (empty($this->data['data'])) {
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
        $timestamp = substr($this->data['ts'], 0, 10);
        foreach ($this->data['data'] as $item) {

            $symbol = strtoupper($item['symbol']);

            if (!isset($pairsMap[$symbol])) {
                //echo $this->exchange."[$symbol]异常\n";
                continue;
            }
            $pair = $pairsMap[$symbol];
            $digitalCurrency = $pair['digital_currency'];
            $marketCurrency = $pair['market_currency'];

            $ticker = new TickerModel();
            $ticker->digital_currency       = $digitalCurrency;
            $ticker->market_currency        = $marketCurrency;

            $ticker->open                   = $item['open'];
            $ticker->high                   = $item['high'];
            $ticker->low                    = $item['low'];
            $ticker->close                  = $item['close'];
            $ticker->amount                 = $item['amount'];
            $ticker->vol                    = $item['vol'];
            $ticker->timestamp              = $timestamp;
            //$ticker->price_pcnt             = $item['close'] - $item['open'];

            $ticker_data["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }

    private function handleError()
    {
        return true;
    }
}