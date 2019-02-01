<?php

namespace ExchangeCenter\Exchanges\B2bx;

use ExchangeCenter\Exchanges\ExchangeBase;
use ExchangeCenter\Helper;
use ExchangeCenter\Models\TickerModel;

class Ticker extends ExchangeBase
{
    protected $exchange = 'b2bx';

    public function getData($options = [])
    {
        $url = $this->config['url'] . $this->config['ticker'];
        $this->request('GET', $url, $options);
        
        if (empty($this->data)) {
            return [];
        }
        return $this->convertData();
    }

    private function convertData()
    {
        $ticker_data = [];
        $ticker = new TickerModel();
        $timestamp = time();

        foreach ($this->data as $datum) {
            if (preg_match('/([A-Z]+)(BTC|USDT|ETH|B2BX)/', $datum['Instrument'], $pairArr)) {                
                $ticker->digital_currency = $pairArr['1'];
                $ticker->market_currency = $pairArr['2'];
                $ticker->open = $datum['SessionOpen'];
                $ticker->high = $datum['SessionHigh'];
                $ticker->low = $datum['SessionLow'];
                $ticker->close = $datum['SessionClose'];
                $ticker->amount = $datum['Rolling24HrVolume'];
                $ticker->bid1 = $datum['BestBid'];
                $ticker->ask1 = $datum['BestOffer'];
                $ticker->vol = $datum['LastTradedPx'] * $datum['Rolling24HrVolume'];
                $ticker->timestamp = $datum['TimeStamp'] > 0 && strlen($datum['TimeStamp']) == 13 ?bcdiv($datum['TimeStamp'], 1000, 0) : $timestamp;

                $ticker_data[$pairArr['1'] . '_' . $pairArr['2']] = Helper::toArray($ticker);
                $ticker_data[$pairArr['1'] . '_' . $pairArr['2']]['price_pcnt'] = bcdiv($datum['Rolling24HrPxChange'], 100, 4);
            }
        }

        return $ticker_data;
    }

    private function handleError()
    {
        if (!empty($this->data['code'])) {
            Helper::fail('请求失败，返回结果：' . json_encode($this->data));
        }
        return true;
    }
}