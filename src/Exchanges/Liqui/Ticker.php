<?php

namespace ExchangeCenter\Exchanges\Liqui;

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
    protected $exchange = 'liqui';

    public function getData($options = [])
    {
        $pairsStr = $this->getPairsStr();
        $url = $this->config['ticker'];

        $this->request('GET', $url . '/' . $pairsStr, $options);

        if (empty($this->data)) {
            return [];
        }
        return $this->convertData();
    }

    /**
     * @author liyu
     * @throws \Exception
     */
    private function getPairsStr()
    {
        $pairs = (new Pairs())->getData();

        if (empty($pairs)) {
            throw new \Exception('获取交易对失败');
        }

        return implode('-', array_map(function ($item) {
            return strtolower("{$item['digital_currency']}_{$item['market_currency']}");
        }, $pairs));
    }

    private function convertData()
    {
        $ticker_data = [];
        foreach ($this->data as $pair=> $item) {

            list (
                $digitalCurrency,
                $marketCurrency
                ) = explode('_', strtoupper($pair));


            $timestamp = $item['updated'];

            $ticker = new TickerModel();
            $ticker->digital_currency       = $digitalCurrency;
            $ticker->market_currency        = $marketCurrency;

            $ticker->open                   = null;
            $ticker->high                   = $item['high'];
            $ticker->low                    = $item['low'];
            $ticker->close                  = $item['last'];
            $ticker->amount                 = $item['vol_cur'];
            $ticker->vol                    = $item['vol'];
            $ticker->timestamp              = $timestamp;
            //$ticker->price_pcnt             = $ticker->close - $ticker->open;
            $ticker->bid1                   = $item['buy'];
            $ticker->ask1                   = $item['sell'];

            $ticker_data["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
        }
        return $ticker_data;
    }
}