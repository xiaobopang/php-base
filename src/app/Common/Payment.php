<?php

declare (strict_types = 1);

namespace App\Common;

use App\Constants\ErrorCode;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Yansongda\Supports\Collection;

/**
 * 支付处理类.
 */
class Payment
{
    protected $pay;

    public function __construct(\Yansongda\HyperfPay\Pay $pay)
    {
        $this->pay = $pay;
    }

    /**
     * @param array $order 支付参数  https://pay.yansongda.cn/docs/v3
     * @param string $driver wechat alipay
     * @param string $type 支付类型
     */
    public function pay(array $order, string $driver, string $type)
    {
        $response = null;
        try {
            switch ($driver) {
                case 'wechat':
                    switch ($type) {
                        case 'mp':
                            $response = $this->pay->wechat()->mp($order);
                            break;
                        case 'wap':
                            $response = $this->pay->wechat()->wap($order);
                            break;
                        case 'app':
                            $response = $this->pay->wechat()->app($order);
                            break;
                        case 'scan':
                            $response = $this->pay->wechat()->scan($order);
                        case 'mini':
                            $response = $this->pay->wechat()->mini($order);
                            break;
                    }
                    break;
                case 'alipay':
                    switch ($type) {
                        case 'web':
                            $response = $this->pay->alipay()->web($order);
                            break;
                        case 'wap':
                            $response = $this->pay->alipay()->wap($order);
                            break;
                        case 'app':
                            $response = $this->pay->alipay()->app($order);
                            break;
                        case 'pos':
                            $response = $this->pay->alipay()->pos($order);
                            break;
                        case 'scan':
                            $response = $this->pay->alipay()->scan($order);
                            break;
                        case 'transfer':
                            $response = $this->pay->alipay()->transfer($order);
                            break;
                        case 'mini':
                            $response = $this->pay->alipay()->mini($order);
                            break;
                    }
                    break;
                default:
                    exception('不存在的支付引擎');
                    break;
            }
        } catch (\Throwable $e) {
            exception($e->getMessage(), ErrorCode::PAY_ERROR);
        }

        //统一处理为数组形式
        if ($response instanceof Collection) {
            $response = $response->toArray();
        } elseif ($response instanceof ResponseInterface) {
            $response = $response->getBody()->getContents();
        } else {
            $response = $response->getBody()->getContents();
        }

        return $response ?? $this->error('支付失败');
    }

    /**
     * 微信支付回调处理.
     */
    public function wechatNotify(\Yansongda\HyperfPay\Pay $pay, ServerRequestInterface $serverRequest)
    {
        //todo 待完善支付回调逻辑  等待对接获得回调参数
        try {
            $notify = $pay->wechat()->callback($serverRequest);

            if ('SUCCESS' !== $notify['trade_state']) {
                $this->error('支付失败');
            }
        } catch (\Exception $exception) {
        }

        return $pay->wechat()->success();
    }

    /**
     * 错误应答.
     */
    private function error(string $message): Response
    {
        return di()->get(ResponseInterface::class)->json(['code' => 10500, 'message' => $message]);
    }
}
