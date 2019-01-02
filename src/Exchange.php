<?php

namespace ExchangeCenter;


/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:25
 */

class Exchange
{
    private $exchange = null;
    private $symbol = null;
    private $class = null;
    private $params = [];
    private $options = [];

    public function setExchange($exchange)
    {
        $exchange = ucfirst($exchange);
        $this->exchange = $exchange;
        return $this;
    }

    public function getExchange()
    {
        return $this->exchange;
    }

    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 获取ticker数据
     * @return mixed
     */
    public function getTicker()
    {
        return $this->initClass('Ticker')->getData($this->options);
    }

    /**
     * 获取pairs数据
     * @return mixed
     */
    public function getPairs()
    {
        return $this->initClass('Pairs')->getData($this->options);
    }

    /**
     * 根据数据类型获取对象
     * @param $mode
     * @return mixed
     */
    private function initClass($mode)
    {
        $class = '\ExchangeCenter\Exchanges\\' . $this->exchange . '\\' . $mode;
        if (!class_exists($class)) {
            Helper::fail('暂不支持该交易所获取Ticker');
        }
        return new $class();
    }

}