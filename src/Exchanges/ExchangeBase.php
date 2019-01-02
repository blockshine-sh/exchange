<?php

namespace ExchangeCenter\Exchanges;

use ExchangeCenter\Helper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use function GuzzleHttp\Promise\unwrap;

/**
 * Created by PhpStorm.
 * User: tiger
 * Date: 2018/12/14
 * Time: 下午2:30
 */
class ExchangeBase
{
    protected $exchange;
    protected $config;
    protected $error;
    protected $data;
    protected $symbol;

    public function __construct()
    {
        $this->config = Helper::config($this->exchange);
    }

    private function getClient($options=[])
    {
        $timeout = $this->config['timeout'] ?? 10;
        $client_params = [
            'timeout' => $timeout,
        ];
        if(!empty($this->config['url'])) {
            $client_params['base_uri'] = $this->config['url'];
        }
        //设置代理
        if (!empty($options['proxy'])) {
            $client_params['proxy'] = $this->config['proxy'];
        }
        return new Client($client_params);
    }

    protected function request($method = 'GET', $url = '', $options = [], $return_type = 'json')
    {
        try {
            $response = $this->getClient()->request($method, $url, $options);
        } catch (ClientException $e) {
            Helper::fail('连接失败：' . $e->getMessage());
        } catch (GuzzleException $e) {
            Helper::fail('请求失败：' . $e->getMessage());
        }
        $data = (string)$response->getBody();
        //@TODO 如果有不是json的再扩展
        $this->data = json_decode($data, true);
        if ($this->data === false) {
            Helper::fail('获取数据异常：' . $data);
        }
        return true;
    }

    /**
     * @param array $multi_params = [
     *  'index' => 1,//索引id，用于辨认返回的数据
     *  'url' => 'xxx',//请求地址
     *  'options' => [],//其他选项
     * ]
     * @return bool
     */
    public function multiRequest(array $multi_params) {
        $client = new Client();
        $promises = [];
        foreach ($multi_params as $param) {
            $options = $param['options'] ?? [];
            $promises[$param['index']] = $client->getAsync($param['url'], $options);
        }

        try {
            $response = unwrap($promises);
        } catch (ClientException $e) {
            Helper::fail('连接失败：' . $e->getMessage());
        } catch (GuzzleException $e) {
            Helper::fail('请求失败：' . $e->getMessage());
        }

        $response_data = [];
        if(!empty($response)) {
            foreach ($response as $index => $data) {
                $data = (string)$data->getBody();
                $response_data[$index] = json_decode($data, true);
            }
        }
        $this->data = $response_data;
        return true;
    }


}