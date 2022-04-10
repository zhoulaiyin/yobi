<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * redis锁定服务
 * 并发访问限制
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */

namespace App\Service;

//use App\Models\System\SystemConfig;

class MessageQueueService
{

    private $format = 'json';

    private $server_address = ''; //服务地址

    private $server_port = ''; //服务端口

    const queueId = '11'; //【已废弃】

    const userId = 'a5d76b232175css2783de13d4c5bc8fd'; //【已废弃】

    public function __construct()
    {
        /*$this->server_address = get_config('go.mq.address');
        $this->server_port = get_config('go.mq.port');*/
    }

    /**
     * 推送推列
     * @param array $push 推送参数
     * @return array
     */
    public function push($push)
    {

        if (empty($this->server_address) || empty($this->server_port)) {
            return ['code' => 10000 , 'message' => '缺少参数'];
        }

        if (!isset($push['call_url']) || empty($push['call_url'])) {
            return ['code' => 10001 , 'message' => '缺少参数：call_url'];
        }

        if ($this->format == 'json') {
            $msg = json_encode($push);
        } else {
            return ['code' => 10002 , 'message' => '未知的消息封装格式'];
        }


        //创建socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket == false) {
            return ['code' => 10003 , 'message' => 'socket创建失败'];
        }

        //连接队列服务
        $connect = socket_connect($socket, $this->server_address, $this->server_port);
        if ($connect == false ) {
            return ['code' => 10004 , 'message' => '连接队列服务失败'];
        }

        //设置socket超时
        $set_timeout = socket_set_option( $socket, SOL_SOCKET,SO_RCVTIMEO, ['sec' => 1000, 'usec' => 100000] );
        if ($set_timeout == false) {
            socket_close($socket);
            return ['code' => 10005 , 'message' => '设置连接超时失败'];
        }

        $send_msg_len = strlen($msg) + 1;
        $send_msg = pack('C', 1) . pack('S', self::queueId) .pack('a32', self::userId) .  pack('a' . $send_msg_len, $msg);

        $msg_len = strlen($send_msg);

        $i=0;
        $send_status = FALSE;
        $send_byte = 0;
        $reset_count = 3;

        //将二进制数据写入到socket中，如果写入字节数小于发送的长度，则默认允许3次重新写入剩余长度
        while ($i < $reset_count) {

            $send_byte += socket_write($socket,$send_msg);
            if ($send_byte == FALSE) {
                socket_close($socket);
                return ['code' => 10006 , 'message' => 'socket 写入失败'];
            }
            if ($send_byte < $msg_len){
                $send_msg = substr($send_msg,$send_byte);
            }
            if ($send_byte == $msg_len){
                $send_status = TRUE;
                break;
            }
            $i++;
        }

        if ($send_status === FALSE) {
            socket_close($socket);
            return ['code' => 10007 , 'message' => '消息长度发送异常'];
        }

        //读取消息投递状态,返回1BYTE 状态标识
        $msg_status = '';
        $msg_read_len = socket_recv($socket, $msg_status, 1, 0);
        if ($msg_read_len != 1 ) {
            socket_close($socket);
            return ['code' => 10008 , 'message' => '读取消息异常'];
        }
        $msg_send_status_ary = unpack('C', $msg_status);

        // 0 : 投递成功  1 : 消息协议错误 2 : 队列不存在 3 : 未知错误
        // 0 : 投递成功  1 : 消息协议错误 2 : 队列不存在 3 : 未知错误
        socket_close($socket);

        switch ($msg_send_status_ary[1]) {
            case 0 :
                return ['code' => 200,'message' => '消息投递成功'];
                break;
            case 1 :
                return ['code' => 10009 ,'message' => '消息协议错误'];
                break;
            case 2 :
                return ['code' => 10010 ,'message' => '队列不存在'];
                break;
            case 3 :
                return ['code' => 10011 ,'message' => '未知错误'];
                break;
            default:
                return ['code' => 10012 ,'message' => '未知错误'];
                break;
        }

    }

}