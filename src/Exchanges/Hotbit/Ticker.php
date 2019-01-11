<?php

namespace ExchangeCenter\Exchanges\Hotbit;

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
    protected $exchange = 'hotbit';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);
        if (empty($this->data['ticker'])) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data['ticker'] as $datum) {
            $_symbol = strtoupper($datum['symbol']);
            $symbol = explode('_', $_symbol);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = $datum['open'];
            $ticker->high = $datum['high'];
            $ticker->low = $datum['low'];
            $ticker->close = $datum['close'];
            $ticker->amount = $datum['vol'];
            $ticker->bid1 = $datum['buy'];
            $ticker->ask1 = $datum['sell'];
            //$ticker->vol = $datum['vq'];
            $ticker->timestamp = $this->data['date'];

            $ticker_data[$_symbol] = Helper::toArray($ticker);
        }

        return $ticker_data;
    }

}