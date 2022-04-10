<?php
/**
 * 发布配置文件
 * Created by zhoulaiyin
 * Date: 2020/1/6
 * Time: 11:55
 */
namespace App\Config\Model;

class PublishConfig {

    //需要同步的集合名称
    public static $collections = [
        "object_index",
        "objects",
        "object_fields",
        "object_fk",
        "object_child",
        "view_list",
        "view_list_permission",
        "view_form",
        "view_form_permission",
        "object_function",
        "object_events",
        "object_tag_group",
        "object_tag",
        "platform_application",
        "flow_tag",
        "flow_category",
        "flow_list",
        "seqno",
        "variable",
        "notice",
        "msg_channel",
        "company",
        "department",
        "user",
        "role",
        "role_notice_map",
        "print_template",
        "api_category",
        "api_sign_way",
        "api_auth",
        "object_contract_template",
        "console_menu",
        "model_menu"
    ];

    /**
     * 创建系统集合并建立索引
     */
    public static function createSystemCollections($DBObj) {
        $system = [
            'events_log' => [
                [ 'key' => ['application_id' => 1 , 'object_id' => 1]],
            ],
            'msg_center' => [
                [ 'key' => ['application_id' => 1 , 'type' => 1]],
            ],
            'object_trace' => [
                [ 'key' => ['object_id' => 1]],
            ],
            'sponsor_log' => [
                [ 'key' => ['creator' => 1]],
            ],
            'process_master' => [
                [ 'key' => ['object_id' => 1]],
            ],
            'process_log' => [
                [ 'key' => ['process_id' => 1]],
                [ 'key' => ['doc_id' => 1]],
            ],
            'msg_queue' => [
                [ 'key' => ['channel_id' => 1]],
                [ 'key' => ['user_list' => 1]],
                [ 'key' => ['copy_to' => 1]],
            ],
            'object_entity_monitor' => [
                [ 'key' => ['created_at' => 1]],
                [ 'key' => ['object_name' => 1]],
                [ 'key' => ['doc_seqno' => 1]],
            ],
            'log_object_events' => [
                [ 'key' => ['application_id' => 1 , 'object_id' => 1]],
            ],
            'async_exception' => [
                [ 'key' => ['application_id' => 1 , 'fail_num' => 1]],
            ],
            'contract' => [
                [ 'key' => ['application_id' => 1 , 'company' => 1]],
            ],
        ];

        //创建系统集合
        foreach ($system as $collection => $indexs) {
            $DBObj->selectCollection($collection)->createIndexes($indexs);
        }
    }

}