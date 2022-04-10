<?php
namespace App\Service\Pay;

use App\Models\OnlinePay\OnlinePayLog;

abstract class OnlinePayService
{

    private $params = [];

    public function __construct($params)
    {
        $this->params = $params;
    }
    
    protected function set($params) {
        $this->params = $params;
    }

    protected function get($key, $default=null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return $default;
    }

    protected function getAll()
    {
        return $this->params;
    }

    /**
     * 保存在线支付请求日志
     * @param int $order_no 订单号
     * @param int $order_type 订单类型
     * @param int $platform 支付平台
     * @param int $pay_type_id 支付方式id
     * @param array $request 请求数据
     * @return bool
     */
    protected function addLog($order_no, $order_type, $platform, $pay_type_id, $request)
    {

        $online_pay_log = OnlinePayLog::find($order_no);
        if (!$online_pay_log) {
            $online_pay_log = new OnlinePayLog();
            $online_pay_log->order_no = $order_no;
        }
        $online_pay_log->order_type = $order_type;
        $online_pay_log->platform = $platform;
        $online_pay_log->pay_type_id = $pay_type_id;
        $online_pay_log->reqeust = print_r($request, true);
        $online_pay_log->pay_status = 1;
        $online_pay_log->success_count = 0;
        $online_pay_log->fail_count = 0;
        $online_pay_log->save();

        return true;

    }

    /**
     * 更新在线支付请求日志
     * @param int $order_no 订单号
     * @param int $pay_status 支付状态 2、失败 3、成功
     * @param string|null $error_msg 错误信息
     * @return bool
     */
    protected function updateLog($order_no, $pay_status, $error_msg = null)
    {

        $online_pay_log = OnlinePayLog::find($order_no);
        if (!$online_pay_log) {
            return false;
        }
        $online_pay_log->server_response = print_r($this->params, true);
        $online_pay_log->pay_status = $pay_status;
        if ($pay_status == 3) {
            $online_pay_log->success_count += 1;
        } else {
            $online_pay_log->success_count += 0;
        }
        $online_pay_log->error_msg = $error_msg;
        $online_pay_log->save();

        return true;

    }

    /**
     * 支付请求
     * @return mixed
     */
    abstract public function request();

    /**
     * 支付异步通知
     * @return mixed
     */
    abstract public function notify();

}