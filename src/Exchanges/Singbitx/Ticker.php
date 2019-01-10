<?php

namespace ExchangeCenter\Exchanges\Singbitx;

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
    protected $exchange = 'singbitx';

    public function getData($options = [])
    {
        $url = $this->config['ticker'];
        $this->request('GET', $url, $options);
        if (empty($this->data)) {
            return [];
        }

        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data as $datum) {
            $symbol = explode('/', $datum['name']);
            $ticker = new TickerModel();
            $ticker->digital_currency = $symbol[0];
            $ticker->market_currency = $symbol[1];
            $ticker->open = $datum['ticker']['open'];
            $ticker->high = $datum['ticker']['high'];
            $ticker->low = $datum['ticker']['low'];
            $ticker->close = $datum['ticker']['last'];
            $ticker->amount = $datum['ticker']['vol'];
            //$ticker->vol = $datum['vq'];
            $ticker->bid1 = $datum['ticker']['buy'];
            $ticker->ask1 = $datum['ticker']['sell'];
            $ticker->timestamp = $datum['at'];
            $ticker->price_pcnt = bcdiv($ticker->close - $ticker->open, $ticker->open, 4);

            $ticker_data[$symbol[0] . '_' . $symbol[1]] = Helper::toArray($ticker);
        }

        return $ticker_data;
    }

}