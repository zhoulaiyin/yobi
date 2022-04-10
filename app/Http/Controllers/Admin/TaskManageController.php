<?php

namespace App\Http\Controllers\Admin;

use App\Models\Classes\Console\System\SysTaskClass;
use App\Models\Classes\Console\System\SysTaskLogClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class TaskManageController extends Controller
{

    public function index()
    {

        $task_type_array = array(
            '1' => '一分钟任务',
            '2' => '五分钟任务',
            '3' => '十分钟任务',
            '4' => '半小时任务',
            '5' => '一小时任务',
            '6' => '0点任务',
            '7' => '1点任务',
            '8' => '3点任务',
        );

        $data = array(
            'task' => $task_type_array,
            'page' => 'task'
        );

        return view('admin/task', $data);

    }

    //查询任务管理列表
    public function search(Request $request)
    {

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => 0,
            'data' => array()
        );

        if (empty($request->input('task_type'))) {
            $return_data['code'] = 400;
            $return_data['msg'] = "参数错误";
            return response()->json($return_data);
        }

        $task_type_array = array(
            '1' => '一分钟任务',
            '2' => '五分钟任务',
            '3' => '十分钟任务',
            '4' => '半小时任务',
            '5' => '一小时任务',
            '6' => '0点任务',
            '7' => '1点任务',
            '8' => '3点任务',
        );

        $task_status = array(
            '1' => '运行',
            '2' => '暂停'
        );

        $where = [];

        //任务id
        if ($request->input('task_id')) {
            $where[] = ['task_id', $request->input('task_id')];
        }

        //任务类型
        if ($request->input('task_type')) {
            $where[] = ['task_type', '=', trim($request->input('task_type'))];
        }

        //任务状态，没获取到
        if ($request->input('task_status')) {
            $where[] = ['task_status', '=', trim($request->input('task_status'))];
        }

        //任务名
        if ($request->input('task_name')) {
            $where[] = ['task_name', 'like', '%' . trim($request->input('task_name')) . '%'];
        }

        //任务链接
        if ($request->input('task_link')) {
            $where[] = ['task_link', 'like', '%' . trim($request->input('task_link')) . '%'];
        }

        $task_data = SysTaskClass::getList($where);

        if ($task_data['count'] > 0) {
            $return_data['count'] = $task_data['count'];

            foreach ($task_data['data'] as $k => $v) {
            $operation = '<a class="layui-btn layui-btn-normal layui-btn-xs" href="javascript: void(0);"  onclick="sysTask.searchLog(1,' ."'" . $v['task_id']. "','" . $v['_id']. "'"  . ')">日志</a>';
            $operation .= '<a class="layui-btn layui-btn-normal layui-btn-xs" href="javascript: void(0);"  onclick="sysTask.edit(' ."'" . $v['task_id']. "','" .  $v['_id']. "'"  . ')">修改</a>';
            $operation .=  '<a class="layui-btn layui-btn-danger layui-btn-xs" href="javascript:void(0);" onclick="sysTask.delConfirm(' . "'" . $v['task_id']. "','".$v['_id']. "'" . ');">删除</a>';

            if ($v['task_status'] == 1) {
              $operation .= '<a class="layui-btn layui-btn-warning layui-btn-xs" href="javascript: void(0);"  onclick="sysTask.changeStatus('."'" . $v['task_status'] ."','" . $v['task_id']. "','". $v['_id']. "'"  . ')">暂停</a>';
            } else {
              $operation .= '<a class="layui-btn layui-btn-success layui-btn-xs" href="javascript: void(0);"  onclick="sysTask.changeStatus('."'" . $v['task_status'] ."','" . $v['task_id']. "','" . $v['_id']. "'"  . ')">运行</a>';
            }

            $return_data['data'][] = array(
              'operation' => $operation,
              '_id' => $v['_id'],
              'task_name' => $v['task_name'],
              'task_link' => $v['task_link'],
              'task_act_value' => $v['task_act_value'],
              'task_status' => $task_status[$v['task_status']],
            );
            $task_data['data'][$k]['task_type_value'] = $task_type_array[$v['task_type']];
            $task_data['data'][$k]['task_status_value'] = $task_status[$v['task_status']];
            }
        }

        return response()->json($return_data);
    }

    //获取任务单条信息
    public function get(Request $request)
    {

        if (empty($request->input('task_id'))) {
            return response()->json([
                'code' => 100003,
                'message' => '参数错误'
            ]);
        }

        $where = [];

        //任务id
        if ($request->input('task_id')) {
            $where[] = ['task_id', '=', $request->input('task_id')];
        }

        $task_data = SysTaskClass::getList($where);

        if (empty($task_data['data'])) {
            return response()->json([
                'code' => 100004,
                'message' => '没有找到任务信息'
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'data' => $task_data['data'][0]
        ]);

    }

    //编辑or添加任务
    public function edit(Request $request)
    {

        if (empty($request->input('task_name')) && empty($request->input('task_link')) && empty($request->input('task_act_value'))) {
            return response()->json([
                'code' => 400,
                'message' => '参数错误'
            ]);
        }

        if (empty($request->input('task_name'))) {
            return response()->json([
                'code' => 100005,
                'message' => '任务名不能为空'
            ]);
        }

        if (empty($request->input('task_link'))) {
            return response()->json([
                'code' => 100006,
                'message' => '任务链接不能为空'
            ]);
        }

        if (empty($request->input('task_act_value'))) {
            return response()->json([
                'code' => 100007,
                'message' => 'act值不能为空'
            ]);
        }

        $userId = UserId();
        if (!$userId) {
            $userId = 'system';
        }

        if ($request->input('task_id')) {
            $tableObj = SysTaskClass::fetch($request->input('_id'));
            if (!$tableObj) {
                return response()->json([
                    "code" => 100008,
                    "message" => '任务信息没有找到'
                ]);
            }
            SysTaskClass::save([
                'task_id' => $request->input('task_id'),
                'task_type' => $request->input('task_type'),
                'task_status' => '2',
                'task_name' => $request->input('task_name'),
                'task_link' => $request->input('task_link'),
                'ask_act_value' => $request->input('task_act_value')
            ]);
        } else {
            SysTaskClass::save([
                'creator' => $userId,
                'task_name' => $request->input('task_name'),
                'task_link' => $request->input('task_link'),
                'task_act_value' => $request->input('task_act_value')
            ], $request->input('task_id'));
        }

        return response()->json([
            "code" => 200,
            "message" => "保存成功"
        ]);

    }

    //暂停或运行任务
    public function status(Request $request)
    {

        if (empty($request->input('task_id')) || empty($request->input('task_status'))) {
            return response()->json([
                "code" => 400,
                "message" => "参数错误"
            ]);
        }

        $tableObj = SysTaskClass::fetch($request->input('_id'));
        if (!$tableObj) {
            return response()->json([
                "code" => 100009,
                "message" => '任务信息没有找到'
            ]);
        }

        if ($tableObj['task_status'] == 1 && $request->input('task_status') == 2) {
            return response()->json([
                "code" => 404,
                "message" => '任务已运行'
            ]);
        }

        if ($tableObj['task_status'] == 2 && $request->input('task_status') == 1) {
            return response()->json([
                "code" => 404,
                "message" => '任务已暂停'
            ]);
        }

        if ($request->input('task_status') == 1) {
            $tableObj['task_status'] = 2;
        } else {
            $tableObj['task_status'] = 1;
        }

        $userId = UserId();
        if (!$userId) {
            $userId = 'system';
        }

        SysTaskClass::save([
            'creator' => $userId,
            'task_status' => $tableObj['task_status']
        ], $request->input('_id'));

        if ($request->input('task_status') == 1) {
            return response()->json([
                "code" => 200,
                "message" => '任务暂停成功'
            ]);
        } else {
            return response()->json([
                "code" => 200,
                "message" => '任务运行成功'
            ]);
        }

    }

    //删除任务
    public function del(Request $request)
    {

        if (empty($request->input('task_id'))) {
            return response()->json([
                "code" => 400,
                "message" => '参数错误'
            ]);
        }

        $tableObj = SysTaskClass::fetch($request->input('_id'));

        if (!$tableObj) {
            return response()->json([
                "code" => 100010,
                "message" => '任务信息没有找到'
            ]);
        }

        SysTaskClass::del($request->input('_id'));

        return response()->json([
            "code" => 200,
            "message" => '删除成功'
        ]);

    }

    //查询日志
    public function log(Request $request)
    {
        $where = [];

        //任务id
        if ($request->input('task_id')) {
            $where[] = ['task_id', '=', $request->input('task_id')];
        }

        //任务日志id
        if ($request->input('task_log_id')) {
            $where[] = ['task_log_id', '=', trim($request->input('task_log_id'))];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'orderBy' => $request->input('sortname'),
            'sort' => $request->input('sortorder')
        ];
        $log_data = SysTaskLogClass::getList($where, $limit);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => $log_data['count'],
            'data' => $log_data['data']
        );


        return response()->json($return_data);
    }


    /**
     * 更新任务日志
     * @param int $task_log_id 日志流水号
     * @param string $result 结果
     * @return array
     */
    public function updateTaskLog($task_log_id, $result)
    {

        if (!isset($task_log_id, $result) || !ebsig_is_int($task_log_id) || empty($result)) {
            return response()->json([
                "code" => 400,
                "message" => '参数错误'
            ]);
        }

        $sys_task_log = SysTaskLogClass::fetch($task_log_id);
        if (!$sys_task_log) {
            return response()->json([
                "code" => 404,
                "message" => '没有找到该任务日志'
            ]);
        }
        $end_time = Carbon::now();
        $total_time = strtotime($end_time) - strtotime($sys_task_log['start_time']);

        SysTaskLogClass::save([
            'end_time' => $end_time,
            'total_time' => $total_time,
            'result' => $result
        ]);


        return response()->json([
            "code" => 200,
            "message" => '任务日志更新成功'
        ]);

    }

}