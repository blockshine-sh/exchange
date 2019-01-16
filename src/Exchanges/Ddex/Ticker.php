<?php

namespace ExchangeCenter\Exchanges\Ddex;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:34
 */
class Ticker extends ExchangeBase
{
    protected $exchange = 'ddex';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);

        $this->check();
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data['data']['tickers'] as $item) {

            list (
                $digitalCurrency,
                $marketCurrency
                ) = explode('-', $item['marketId']);

            $ticker = new TickerModel();
            $ticker->digital_currency       = $digitalCurrency;
            $ticker->market_currency        = $marketCurrency;
            $ticker->open                   = null;
            $ticker->high                   = $item['high'];
            $ticker->low                    = $item['low'];
            $ticker->close                  = $item['price'];
            $ticker->amount                 = $item['volume'];
            $ticker->vol                    = $item['volume'] * $item['price'];
            $ticker->timestamp              = time();
            $ticker->bid1                   = $item['bid'];
            $ticker->ask1                   = $item['ask'];
            $ticker_data["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }

    public function check()
    {
        if (empty($this->data)) {
            throw new Exception($this->exchange.'暂无数据');
        }

        if ($this->data['status'] !=0) {
            throw new Exception($this->exchange.$this->data['desc']);
        }
    }
}