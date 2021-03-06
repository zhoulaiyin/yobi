<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

class EbsigMsgPush extends Controller
{

    /**
     * 数据格式
     */
    public $format = 'json';

    private $gomq_addr = '';
    private $gomq_port = '';

    /**
     * 推送消息
     * @param array $push_array 消息内容
     * @param string $mq_server_addr goMQ服务地址
     * @param string $mq_server_port goMQ服务端口
     * @param int $queueId 【已废弃】
     * @param string $userId 【已废弃】
     * @return array
     */
    private function async($push_array)
    {

        if (!isset($push_array) || !is_array($push_array)) {
            return array('code' => 400, 'message' => '参数格式错误');
        }

        if (!isset($push_array['call_url'])) {
            return array('code' => 400, 'message' => '参数错误');
        }

        $queueId = 11;
        $userId = 'a5d76b232175css2783de13d4c5bc8fd';

        switch ($this->format) {

            case 'json' :
                $msg = $msg = json_encode($push_array);
                break;

            default :
                return array('code' => 400, 'message' => '未知的消息封装格式');
                break;

        }

        //创建socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket == false) {
            return array('code' => 404, 'message' => 'socket创建失败');
        }

        //连接队列服务
        $connect = socket_connect($socket, $this->gomq_addr, $this->gomq_port);
        if ($connect == false) {
            return array('code' => 404, 'message' => '连接队列服务失败');
        }

        //设置socket超时
        $set_timeout = socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 1000, 'usec' => 100000));
        if ($set_timeout == false) {
            socket_close($socket);
            return array('code' => 400, 'message' => '设置连接超时失败');
        }

        $send_msg_len = strlen($msg) + 1;
        $send_msg = pack('C', 1) . pack('S', $queueId) . pack('a32', $userId) . pack('a' . $send_msg_len, $msg);

        $msg_len = strlen($send_msg);

        $i = 0;
        $send_status = FALSE;
        $send_byte = 0;
        $reset_count = 3;

        //将二进制数据写入到socket中，如果写入字节数小于发送的长度，则默认允许3次重新写入剩余长度
        while ($i < $reset_count) {

            $send_byte += socket_write($socket, $send_msg);
            if ($send_byte == FALSE) {
                socket_close($socket);
                return array('code' => 400, 'message' => 'socket 写入失败');
            }
            if ($send_byte < $msg_len) {
                $send_msg = substr($send_msg, $send_byte);
            }
            if ($send_byte == $msg_len) {
                $send_status = TRUE;
                break;
            }
            $i++;
        }

        if ($send_status === FALSE) {
            socket_close($socket);
            return array('code' => 400, 'message' => '消息长度发送异常');
        }

        //读取消息投递状态,返回1BYTE 状态标识
        $msg_status = '';
        $msg_read_len = socket_recv($socket, $msg_status, 1, 0);
        if ($msg_read_len != 1) {
            socket_close($socket);
            return array('code' => 400, 'message' => '读取消息异常');
        }
        $msg_send_status_ary = unpack('C', $msg_status);

        // 0 : 投递成功  1 : 消息协议错误 2 : 队列不存在 3 : 未知错误
        // 0 : 投递成功  1 : 消息协议错误 2 : 队列不存在 3 : 未知错误

        socket_close($socket);

        switch ($msg_send_status_ary[1]) {
            case 0 :
                return array('code' => 200, 'message' => '消息投递成功');
                break;
            case 1 :
                return array('code' => 400, 'message' => '消息协议错误');
                break;
            case 2 :
                return array('code' => 404, 'message' => '队列不存在');
                break;
            case 3 :
                return array('code' => 500, 'message' => '未知错误');
                break;
            default:
                return array('code' => 500, 'message' => '未知错误');
                break;
        }

    }

    /**
     * 推送消息给ebsig的goMQ服务
     * @param array $push_array 消息内容
     * @return array
     */
    public function ebsigAsyncPush($push_array)
    {
        $this->gomq_addr = env('GOMQ_ADDR', '127.0.0.1');
        $this->gomq_port = env('GOMQ_PORT', 9009);
        return $this->async($push_array);
    }

}