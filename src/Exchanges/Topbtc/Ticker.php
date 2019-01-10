<?php

namespace ExchangeCenter\Exchanges\Topbtc;

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
    protected $exchange = 'topbtc';

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
            $ticker = new TickerModel();
            $ticker->digital_currency = $datum['coin'];
            $ticker->market_currency = $datum['market'];
            $ticker->open = $datum['ticker']['last'];//@TODO 此交易所ticker没有open价格，先给个last
            $ticker->high = $datum['ticker']['high'];
            $ticker->low = $datum['ticker']['low'];
            $ticker->close = $datum['ticker']['last'];
            $ticker->amount = $datum['ticker']['vol'];
            $ticker->bid1 = $datum['ticker']['buy'];
            $ticker->ask1 = $datum['ticker']['sell'];
            //$ticker->vol = $datum['ticker']['vol'];
            $ticker->timestamp = $datum['date'];
            $ticker->price_pcnt = bcdiv($ticker->close - $ticker->open, $ticker->open, 4);
            $ticker_data[$datum['coin'] . '_' . $datum['market']] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }

    private function handleError()
    {
        return true;
    }

}