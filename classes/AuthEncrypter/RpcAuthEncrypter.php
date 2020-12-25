<?php

namespace Ecjia\App\Rpc\AuthEncrypter;

use Ecjia\App\Rpc\Repositories\DefaultRpcAccountRepository;
use Illuminate\Encryption\Encrypter;

class RpcAuthEncrypter implements \Ecjia\Component\AutoLogin\AuthEncrypterInterface
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
    public function getAuthKey()
    {
        return $this->account->appsecret;
    }

    /**
     * @return string
     */
    public function getCipher()
    {
        return config('system.cipher');
    }


    /**
     * @return Encrypter
     */
    public function getEncrypter()
    {
        return new Encrypter($this->getAuthKey(), $this->getCipher());
    }

}