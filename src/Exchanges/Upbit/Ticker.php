<?php

namespace ExchangeCenter\Exchanges\Upbit;

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
    protected $exchange = 'upbit';

    public function getData($options = [])
    {
        $pairs = (new Pairs($this->exchange))->getData();
        if (empty($pairs)) return [];

        $promises = [];
        foreach ($pairs as $pair) {
            $this->request('GET',
                sprintf('%s%s-%s', $this->config['ticker'], $pair['market_currency'], $pair['digital_currency']),
                $options
            );
            if (!empty($this->data)) {
                $promises[] = $this->data;
            }echo \GuzzleHttp\json_encode($this->data).PHP_EOLgit ;
            //sleep(1);
        }
        $this->data = $promises;
        return $this->convertData();
    }

    private function convertData()
    {
        $tickers = [];
        if (!empty($this->data)) {
            foreach ($this->data as $item) {

                $ticker = new TickerModel();
                $data = current($item);

                list (
                    $marketCurrency,
                    $digitalCurrency
                    ) = explode('-', $data['market']);

                $ticker->digital_currency = $digitalCurrency;
                $ticker->market_currency = $marketCurrency;

                $ticker->open       = $data['trade_price'];
                $ticker->high       = $data['high_price'];
                $ticker->low        = $data['low_price'];
                $ticker->close      = $data['prev_closing_price'];
                $ticker->amount     = $data['acc_trade_volume_24h'];
                $ticker->vol        = $data['acc_trade_price_24h'];
                $ticker->timestamp  = substr($data['timestamp'], 0, 10);
                $ticker->price_pcnt = $data['signed_change_price'];

                $tickers["{$digitalCurrency}_{$marketCurrency}"] = Helper::toArray($ticker);
            }
        }
        return $tickers;
    }
}