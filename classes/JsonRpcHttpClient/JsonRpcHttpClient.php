<?php

namespace Ecjia\App\Rpc\JsonRpcHttpClient;

use ecjia;
use Ecjia\App\Rpc\Repositories\DefaultRpcAccountRepository;
use Royalcms\Laravel\JsonRpcClient\BasicAuthentication;

class JsonRpcHttpClient extends \Royalcms\Laravel\JsonRpcClient\JsonRpcHttpClient
{
    protected $account;

    public function __construct()
    {
        $appid = ecjia::config('cashier_dscmall_rpc_appid');
        $this->account = (new DefaultRpcAccountRepository())->getAccountByAppId($appid);

        if (empty($this->account)) {
            throw new \InvalidArgumentException('请求大商创RPC服务帐号未配置，请至设置中选择！');
        }
    }

    /**
     * @return string
     */
    protected function getUri()
    {
        return $this->account->callback_url;
    }

    /**
     * 自定义请求Header信息
     * @return string[]
     */
    protected function getHeaders()
    {
        return (new BasicAuthentication($this->account->appid, $this->account->appsecret))->getAuthorizationHeaders();
    }


}