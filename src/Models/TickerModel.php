<?php
/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午5:02
 */

namespace ExchangeCenter\Models;


class TickerModel
{
    public $digital_currency = null;
    public $market_currency = null;
    public $open   = 0.00000000; //开
    public $high   = 0.00000000; //高
    public $low    = 0.00000000; //低
    public $close  = 0.00000000; //收
    public $amount = 0.00000000; //成交量
    public $vol    = 0.00000000; //成交额
    public $count  = 0;          //成交笔数
    public $bid1   = 0.00000000;  //买一价
    public $ask1   = 0.00000000;  //卖一价
    public $timestamp = 0;       //时间戳

}