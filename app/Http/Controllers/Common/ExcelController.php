<?php

namespace App\Http\Controllers\Common;


use DB;

use Excel;

use App\Http\Requests;

use Illuminate\Http\Request;

use App\Http\Models\User\User;

use App\Http\Controllers\Controller;

use App\Http\Models\Demand\Demand;

use App\Models\Sms\Log;
use App\Models\Sms\Account;
use App\Models\Sms\Setting;
use App\Models\Sms\Deposit;
use App\Models\Sms\Package;
use App\Models\PayType;

use App\Http\Models\Company\Company;

use App\Http\Models\Company\CompanyCommunication;

use Illuminate\Support\Facades\Redis as Redis;

class ExcelController extends Controller
{

    public function export(Request $request, $op)
    {

        $file_name = '';
        $res = [];
        switch ($op) {
            case 1: //客户需求中心

                if ($request->input('demand_status') > 0) {  //不同状态
                    $status = $request->input('demand_status');
                } else {  //所有需求
                    $status = 0;
                }

                $res = $this->demand($status);
                //excel表名
                $file_name = '项目需求信息列表';
                break;

            case 2://控制中心需求

                $res = $this->demandCenter($request->all());

                //excel表名
                $file_name = '需求列表';

                break;

            case 3://发送日志导出

                $res = $this->log($request->all());

                //excel表名
                $file_name = '发送日志';

                break;

            case 4://账户列表导出

                $res = $this->account($request->all());

                //excel表名
                $file_name = '账户列表';

                break;

            case 5://提醒设置导出

                $res = $this->remind($request->all());

                //excel表名
                $file_name = '提醒设置';

                break;

            case 6://充值列表导出

                $res = $this->deposit($request->all());

                //excel表名
                $file_name = '充值列表';

                break;

            case 7://充值套餐导出

                $res = $this->package($request->all());

                //excel表名
                $file_name = '充值套餐';

                break;

            case 8://客户管理列表导出

                $res = $this->company($request->all());

                //excel表名
                $file_name = '客户管理列表';

                break;

            case 9://客户管理列表导出

                $res = $this->companyOne($request->all());

                //excel表名
                $file_name = '客户沟通信息列表';

                break;

            default;
                break;
        }

        Excel::create($file_name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {

                if (!is_null($res[1])) {
                    $sheet->setWidth($res[1]);
                }

                $sheet->rows($res[0]);

            });
        })->export('xls');
    }

    public function import()
    {

    }

    private function demand($status)
    {
        //获取用户id
        $userId = Redis::get('USER_ID' . session()->getId());

        //获取用户相关信息
        $user_data = DB::table('project_user')
            ->select('project_id')
            ->where('userID', $userId)
            ->first();

        if ($user_data['project_id'] > 0) {

            if ($status == 0) { //查询所有需求
                $demand_array = DB::table('project_demand')
                    ->select('demand_id', 'createTime', 'priority', 'demand_name', 'audit_person', 'auditTime', 'working_day', 'completion_date', 'pre_user_name', 'pre_time')
                    ->where('project_id', $user_data['project_id'])
                    ->get();
            } elseif ($status == 2) { //查询已拒绝的需求
                $demand_array = DB::table('project_demand')
                    ->select('demand_id', 'createTime', 'priority', 'demand_name', 'audit_person', 'auditTime', 'working_day', 'completion_date', 'pre_user_name', 'pre_time')
                    ->whereIn('demand_status', [2, 4])
                    ->where('project_id', $user_data['project_id'])
                    ->get();
            } else {//查询不同状态下的需求
                $demand_array = DB::table('project_demand')
                    ->select('demand_id', 'createTime', 'priority', 'demand_name', 'audit_person', 'auditTime', 'working_day', 'completion_date', 'pre_user_name', 'pre_time')
                    ->where('demand_status', $status)
                    ->where('project_id', $user_data['project_id'])
                    ->get();
            }

        } else {

            if ($status == 0) { //查询所有需求
                $demand_array = DB::table('project_demand')
                    ->select('demand_id', 'createTime', 'priority', 'demand_name', 'audit_person', 'auditTime', 'working_day', 'completion_date', 'pre_user_name', 'pre_time')
                    ->get();
            } elseif ($status == 2) { //查询已拒绝的需求
                $demand_array = DB::table('project_demand')
                    ->select('demand_id', 'createTime', 'priority', 'demand_name', 'audit_person', 'auditTime', 'working_day', 'completion_date', 'pre_user_name', 'pre_time')
                    ->whereIn('demand_status', [2, 4])
                    ->get();
            } else {//查询不同状态下的需求
                $demand_array = DB::table('project_demand')
                    ->select('demand_id', 'createTime', 'priority', 'demand_name', 'audit_person', 'auditTime', 'working_day', 'completion_date', 'pre_user_name', 'pre_time')
                    ->where('demand_status', $status)
                    ->get();
            }

        }

        //组装execl表头部信息
        if ($status == 1 || $status == 3) {
            $cellData = [['编号', '创建时间', '优先级', '需求名称', '预审人', '预审时间', '工作量(人/日)', '完成时间']];
        } else {
            $cellData = [['编号', '创建时间', '优先级', '需求名称', '审核人', '审核时间', '工作量(人/日)', '完成时间']];
        }

        //循环数据
        foreach ($demand_array as $data) {
            if ($data['priority'] == 1) {
                $priority = '低';
            } elseif ($data['priority'] == 2) {
                $priority = '中';
            } elseif ($data['priority'] == 3) {
                $priority = '高';
            }

            if ($status == 1 || $status == 3) {
                array_push($cellData, array($data['demand_id'], $data['createTime'], $priority, $data['demand_name'], $data['pre_user_name'], $data['pre_time'], $data['working_day'], $data['completion_date']));
            } else {
                array_push($cellData, array($data['demand_id'], $data['createTime'], $priority, $data['demand_name'], $data['audit_person'], $data['auditTime'], $data['working_day'], $data['completion_date']));
            }

        }

        //设置列宽
        $row_width = [
            'A' => 10,
            'B' => 20,
            'C' => 10,
            'D' => 55,
            'E' => 15,
            'F' => 20,
            'G' => 10,
            'H' => 20,
        ];

        return [$cellData, $row_width];
    }

    //控制中心需求导出
    public function demandCenter($demand = array())
    {

        //获取当前登录的用户ID
        $user_id = Redis::get('ADMIN_USER_ID' . session()->getId());

        $where = [];

        if (!empty($demand['head_search'])) {

            if ($demand['head_search'] == 1) {//由我发起

                if (empty($demand['creator'])) {//防止由我发起和创建人冲突
                    $where[] = ['creator', $user_id];
                }

            } else if ($demand['head_search'] == 2) {//待我处理

                $where[] = ['person_in_charge_id', $user_id];

            } else if ($demand['head_search'] == 3) {//超期

                $today = date('Y-m-d');//获取今天日期

                $where[] = ['finish_date', '<', $today];

                if ($demand['status'] == 1) {
                    $where[] = ['demand_status', '<', 9];
                }

            } else if ($demand['head_search'] == 4) {//临期

                $date = date('Y-m-d', strtotime('3 day'));//获取三天后

                $where[] = ['finish_date', '<=', $date];

                if ($demand['status'] == 1) {
                    $where[] = ['demand_status', '<', 9];
                }

            } else if ($demand['head_search'] == 5) {//优先级最高

                $where[] = ['priority', 5];

            } else if ($demand['head_search'] == 6) {//优先级高

                $where[] = ['priority', 4];

            } else if ($demand['head_search'] == 7) {//挂起
                $where[] = ['hang_up', 1];
            }

        }

        if (!empty($demand['project_id'])) {
            $where[] = ['project_id', $demand['project_id']];
        }
        if (!empty($demand['creator'])) {
            $where[] = ['creator', $demand['creator']];
        }
        if (!empty($demand['person_in_charge_id'])) {
            $where[] = ['person_in_charge_id', $demand['person_in_charge_id']];
        }
        if (!empty($demand['demand_name'])) {
            $where[] = ['demand_name', 'like', '%' . $demand['demand_name'] . '%'];
        }
        if (!empty($demand['reject'])) {
            $where[] = ['reject', $demand['reject']];
        }
        if (!empty($demand['tag'])) {
            $where[] = ['tags', 'like', '%' . $demand['tag'] . '%'];
        }

        if ($demand['status'] != 1) {
            $where[] = ['demand_status', $demand['status']];
        }

        $demand_data = Demand::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['需求编号', '需求名称', '需求状态', '产品', '项目', '优先级', '创建人', '负责人', '要求完成日期', '阶段要求完成日期']];

        //循环数据
        foreach ($demand_data as $data) {

            if ($data['demand_status'] == 1) {
                $demand_status = '待确认';
            } else if ($data['demand_status'] == 2) {
                $demand_status = '待审核';
            } else if ($data['demand_status'] == 3) {
                $demand_status = '原型设计';
            } else if ($data['demand_status'] == 4) {
                $demand_status = 'UI设计';
            } else if ($data['demand_status'] == 5) {
                $demand_status = '待开发';
            } else if ($data['demand_status'] == 6) {
                $demand_status = '开发中';
            } else if ($data['demand_status'] == 7) {
                $demand_status = '待测试';
            } else if ($data['demand_status'] == 8) {
                $demand_status = '待发布';
            } else if ($data['demand_status'] == 9) {
                $demand_status = '已完成';
            } else if ($data['demand_status'] == 10) {
                $demand_status = '已取消';
            }

            if ($data['priority'] == 1) {
                $priority = '最低';
            } else if ($data['priority'] == 2) {
                $priority = '低';
            } else if ($data['priority'] == 3) {
                $priority = '中';
            } else if ($data['priority'] == 4) {
                $priority = '高';
            } else if ($data['priority'] == 5) {
                $priority = '最高';
            }

            $creator_name = User::find($data['creator']);//获取创建者姓名

            array_push($cellData, array($data['demand_id'], $data['demand_name'], $demand_status, $data['product_name'], $data['project_name'], $priority, $creator_name['trueName'], $data['person_in_charge'], $data['finish_date'], $data['stage_finish_date']));

        }

        //设置列宽
        $row_width = [
            'A' => 10,
            'B' => 50,
            'C' => 20,
            'D' => 30,
            'E' => 30,
            'F' => 10,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
        ];

        return [$cellData, $row_width];

    }

    //发送日志导出
    public function log($args_array = array())
    {

        $where = [];
        if (!empty($args_array['mobile'])) {
            $where[] = ['mobile', $args_array['mobile']];
        }
        if (!empty($args_array['account_id'])) {
            $where[] = ['account_id', $args_array['account_id']];
        }
        if (!empty($args_array['company_name'])) {
            $role = Account::where('company_name', 'like', '%' . $args_array['company_name'] . '%')->first();
            if (!empty($role)) {
                $where[] = ['account_id', $role['id']];
            } else {
                return response()->json(['total' => 0, 'rows' => array()]);
            }
        }

        $log_data = Log::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['账户ID', '日志ID', '手机号', '公司名称', '短信内容', '返回消息', '发送状态', '注册时间']];

        //循环数据
        foreach ($log_data as $data) {

            if ($data['send_status'] == 1) {
                $stat = '成功';
            } else if ($data['send_status'] == 2) {
                $stat = '失败';
            }

            //查询公司名称
            $company_data = Account::find($data['account_id']);

            array_push($cellData, array($data['account_id'], $data['id'], $data['mobile'], $company_data['company_name'], $data['content'], $data['message'], $stat, $data['created_at']));

        }

        //设置列宽
        $row_width = [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20
        ];

        return [$cellData, $row_width];
    }

    //账户列表导出
    public function account($args_array = array())
    {

        $where = [];

        if (!empty($args_array['mobile'])) {
            $where[] = ['mobile', $args_array['mobile']];
        }
        if (!empty($args_array['company_name'])) {
            $where[] = ['company_name', 'like', '%' . $args_array['company_name'] . '%'];
        }

        $account_data = Account::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['账户ID', '手机号', '开通短信业务类型', '剩余短信(条)', '累计充值(元)', '公司名称', '短信签名', '注册时间', '使用状态']];

        //循环数据
        foreach ($account_data as $data) {

            if ($data['sms_type'] == 1) {
                $stat = '通知类短信';
            } else if ($data['sms_type'] == 2) {
                $stat = '营销类短信';
            }
            if ($data['useFlg'] == 1) {
                $useFlg = '启用';
            } else if ($data['useFlg'] == 0) {
                $useFlg = '禁用';
            }

            array_push($cellData, array($data['id'], $data['mobile'], $stat, $data['sms_number'], $data['sum_amount'], $data['company_name'], $data['signature'], $data['created_at'], $useFlg));

        }

        //设置列宽
        $row_width = [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20
        ];

        return [$cellData, $row_width];
    }

    //提醒设置导出
    public function remind($args_array = array())
    {

        $where = [];
        if (!empty($args_array['id'])) {
            $where[] = ['id', $args_array['id']];
        }

        $setting_data = Setting::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['账户ID', '公司名称', '开启短信每日使用量提醒', '开启预警提醒', '预警提醒数量', '提醒手机号']];

        //循环数据
        foreach ($setting_data as $data) {

            if ($data['open_daily_reminder'] == 1) {
                $stat = '是';
            } else if ($data['open_daily_reminder'] == 0) {
                $stat = '否';
            }
            if ($data['open_warning_reminder'] == 1) {
                $warning = '是';
            } else if ($data['open_warning_reminder'] == 0) {
                $warning = '否';
            }

            //查询公司名称
            $company_data = Account::find($data['id']);

            array_push($cellData, array($data['id'], $company_data['company_name'], $stat, $warning, $data['warning_reminder_number'], $data['reminder_mobile']));

        }

        //设置列宽
        $row_width = [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20
        ];

        return [$cellData, $row_width];
    }

    //充值列表导出
    public function deposit($args_array = array())
    {

        $where = [];

        if (!empty($args_array['bill_no'])) {
            $where[] = ['id', $args_array['bill_no']];
        }
        if (!empty($args_array['pay_type_id'])) {
            $where[] = ['pay_type_id', $args_array['pay_type_id']];
        }
        if (!empty($args_array['account_id'])) {
            $where[] = ['account_id', $args_array['account_id']];
        }
        if ($args_array['recharge_status'] != 0) {
            $where[] = ['recharge_status', $args_array['recharge_status']];
        }

        if (!empty($args_array['mobile'])) {
            $role = Account::where(['mobile' => $args_array['mobile']])->first();
            if (!empty($role)) {
                $where[] = ['account_id', $role['id']];
            } else {
                return response()->json(['total' => 0, 'rows' => array()]);
            }
        }

        $deposit_data = Deposit::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['订单号', '下单时间', '订单状态', '账户ID', '手机号', '公司名称', '充值短信数量', '套餐名称', '支付流水号', '支付时间', '支付方式']];

        //循环数据
        foreach ($deposit_data as $data) {

            if ($data['recharge_status'] == 1) {
                $status_type = '未支付';
            } else if ($data['recharge_status'] == 2) {
                $status_type = '已支付';
            } else if ($data['recharge_status'] == 10) {
                $status_type = '已关闭';
            } else {
                $status_type = '';
            }

            $pay_type = PayType::find($data['pay_type_id']);
            //查询公司名称
            $company_data = Account::find($data['account_id']);

            array_push($cellData, array($data['id'], $data['created_at'], $status_type, $data['account_id'], $company_data['mobile'], $company_data['company_name'], $data['sms_number'], $data['package_name'], $data['pay_code'], $data['pay_time'], $pay_type['pay_type_name']));

        }

        //设置列宽
        $row_width = [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20
        ];

        return [$cellData, $row_width];
    }

    //充值套餐导出
    public function package($args_array = array())
    {

        $where = [];

        $package_data = Package::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['套餐名', '单价(元/条)', '短信条数', '应付金额', '折扣信息', '折扣后实付金额(元)', '使用状态']];

        //循环数据
        foreach ($package_data as $data) {

            if ($data['discount'] == 0) {
                $discount = '--';
            } else {
                $discount = floatval($data['discount']) . '折';
            }
            if ($data['useFlg'] == 1) {
                $useFlg = '启用';
            } elseif ($data['useFlg'] == 0) {
                $useFlg = '禁用';
            }

            array_push($cellData, array($data['package_name'], $data['unit_price'], $data['sms_number'], $data['amount_payable'], $discount, $data['amount_paid'], $useFlg));

        }

        //设置列宽
        $row_width = [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20
        ];

        return [$cellData, $row_width];
    }

    //客户管理列表导出
    public function company($args_array = array())
    {

        $where = [];

        if (!empty($args_array['company_name'])) {
            $where[] = ['company_name', 'like', '%' . $args_array['company_name'] . '%'];
        }
        if (!empty($args_array['account_manager'])) {

            $user = User::find($args_array['account_manager']);

            $where[] = ['account_manager', '=', $user->trueName];
        }
        if (!empty($args_array['opportunity_pretrial'])) {
            $where[] = ['opportunity_pretrial', $args_array['opportunity_pretrial']];
        }
        if (!empty($args_array['client_stage'])) {
            $where[] = ['client_stage', $args_array['client_stage']];
        }
        if (!empty($args_array['product'] && $args_array['product'] != 100)) {
            $where[] = ['product_id_string', 'like', '%' . $args_array['product'] . '%'];
        }
        if (!empty($args_array['product'] && $args_array['product'] == 100)) {
            $where[] = ['product', 'like', '%' . json_encode($args_array['product']) . '%'];
        }
        if (!empty($args_array['next_start_time'])) {
            $where[] = ['next_start_time', '=', $args_array['next_start_time']];
        }
        if (!empty($args_array['status'])) {
            $where[] = ['status', '=', $args_array['status']];
        }

        $company_data = Company::where($where)->get()->toArray();

        //组装execl表头部信息
        $cellData = [['序号', '客户名称', '商机预审', '客户阶段', '所属行业', '客户经理', '下次跟进节点', '	状态', '所购产品', '问题记录', '所需资源支持']];

        $product = [
            1 => '微信平台',
            2 => 'PC平台',
            3 => 'APP',
            4 => '支付宝服务窗',
            100 => '其他',
        ];

        //循环数据
        foreach ($company_data as $data) {

            //客户阶段
            $client_stage = '初次接触';
            if ($data['client_stage'] == 2) {
                $client_stage = '持续跟进';
            } elseif ($data['client_stage'] == 3) {
                $client_stage = '方案呈现';
            } elseif ($data['client_stage'] == 4) {
                $client_stage = '深度沟通';
            } elseif ($data['client_stage'] == 5) {
                $client_stage = '报价提交';
            } elseif ($data['client_stage'] == 6) {
                $client_stage = '商务谈判';
            } elseif ($data['client_stage'] == 7) {
                $client_stage = '合同签约';
            } elseif ($data['client_stage'] == 8) {
                $client_stage = '项目丢单';
            }

            //所属行业
            $industry = '无';
            if ($data['industry'] == 1) {
                $industry = '服饰鞋帽';
            } elseif ($data['industry'] == 2) {
                $industry = '商超便利';
            } elseif ($data['industry'] == 3) {
                $industry = '购物中心';
            } elseif ($data['industry'] == 4) {
                $industry = '生鲜烘培';
            } elseif ($data['industry'] == 5) {
                $industry = '医药连锁';
            } elseif ($data['industry'] == 6) {
                $industry = '酒类连锁';
            } elseif ($data['industry'] == 7) {
                $industry = '其他';
            }

            //合作模式
            $client_type = '无';
            if ($data['client_type'] == 1) {
                $client_type = 'SAAS客户：租用';
            } elseif ($data['client_type'] == 2) {
                $client_type = '项目客户：买断';
            } elseif ($data['client_type'] == 3) {
                $client_type = '战略客户：长期定制';
            }

            //所购商品
            $product_id_string = [];
            if (!empty($data['product'])) {

                $data['product'] = json_decode($data['product'], true);
                foreach ($data['product']['id'] as $id) {
                    if (isset($product[$id])) {
                        $product_id_string[] = $product[$id];
                    }
                }
                if (!empty($data['product']['other'])) {
                    $product_id_string[] = $data['product']['other'];
                }
            }
            $product_data = !empty($data['product']) ? implode(',', $product_id_string) : '';

            //商机预审
            if ($data['opportunity_pretrial'] == 1) {
                $opportunity_pretrial = '重要';
            } elseif ($data['opportunity_pretrial'] == 2) {
                $opportunity_pretrial = '一般';
            } else {
                $opportunity_pretrial = '无效';
            }

            $status = '已完成';
            if ($data['status'] == 2) {
                $status = '未完成';
            }

            $next_start_time = '无';
            if ($data['next_start_time'] == 1) {
                $next_start_time = '三天内';
            } else if ($data['next_start_time'] == 2) {
                $next_start_time = '一周内';
            } else if ($data['next_start_time'] == 3) {
                $next_start_time = '半个月内';
            } else if ($data['next_start_time'] == 4) {
                $next_start_time = '一个月内';
            } else if ($data['next_start_time'] == 5) {
                $next_start_time = '一个月以上';
            }

            $company_communication = CompanyCommunication::select('problem_type', 'custom_problem', 'support', 'other_support')
                ->where(['company_id' => $data['company_id']])
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get()->toArray();

            $problem_type = '';
            $support = '';

            if (!empty($company_communication)) {

                $problem_type = '无法接触到关键决策人';
                if ($company_communication[0]['problem_type'] == 2) {
                    $problem_type = '项目延期或有变化';
                } elseif ($company_communication[0]['problem_type'] == 3) {
                    $problem_type = '联系人变更或决策流程变更';
                } elseif ($company_communication[0]['problem_type'] == 4) {
                    $problem_type = '客户需求不明确';
                } elseif ($company_communication[0]['problem_type'] == 5) {
                    $problem_type = '竞争对手因素';
                } elseif ($company_communication[0]['problem_type'] == 6) {
                    $problem_type = '需求无法确认';
                } elseif ($company_communication[0]['problem_type'] == 7) {
                    $problem_type = '价格因素';
                } elseif ($company_communication[0]['problem_type'] == 8) {
                    $problem_type = '合同条款争议';
                } elseif ($company_communication[0]['problem_type'] == 9) {
                    $problem_type = '自定义问题:' . $company_communication[0]['custom_problem'];
                }

                $support = '解决方案';
                if ($company_communication[0]['support'] == 2) {
                    $support = '报价清单';
                } elseif ($company_communication[0]['support'] == 3) {
                    $support = '系统演示';
                } elseif ($company_communication[0]['support'] == 4) {
                    $support = '测试账号';
                } elseif ($company_communication[0]['support'] == 5) {
                    $support = '现场拜访';
                } elseif ($company_communication[0]['support'] == 6) {
                    $support = '其他支持:' . $company_communication[0]['other_support'];
                }
            }


            array_push($cellData, array($data['company_id'], $data['company_name'], $opportunity_pretrial, $client_stage, $industry, $data['account_manager'], $next_start_time, $status, $product_data, $problem_type, $support));

        }

        //设置列宽
        $row_width = [
            'A' => 10,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
        ];

        return [$cellData, $row_width];
    }

    //客户管理列表导出
    public function companyOne($args_array = array())
    {

        $company_data = Company::find($args_array['company_id']);

        $data = $company_data->toArray();

        //组装execl表头部信息
        $cellData = [['序号', '客户名称', '商机预审', '客户阶段', '所属行业', '客户经理', '下次跟进节点', '	状态', '所购产品', '问题记录', '所需资源支持']];

        $product = [
            1 => '微信平台',
            2 => 'PC平台',
            3 => 'APP',
            4 => '支付宝服务窗',
            100 => '其他',
        ];

        //客户阶段
        $client_stage = '初次接触';
        if ($data['client_stage'] == 2) {
            $client_stage = '持续跟进';
        } elseif ($data['client_stage'] == 3) {
            $client_stage = '方案呈现';
        } elseif ($data['client_stage'] == 4) {
            $client_stage = '深度沟通';
        } elseif ($data['client_stage'] == 5) {
            $client_stage = '报价提交';
        } elseif ($data['client_stage'] == 6) {
            $client_stage = '商务谈判';
        } elseif ($data['client_stage'] == 7) {
            $client_stage = '合同签约';
        } elseif ($data['client_stage'] == 8) {
            $client_stage = '项目丢单';
        }

        //所属行业
        $industry = '无';
        if ($data['industry'] == 1) {
            $industry = '服饰鞋帽';
        } elseif ($data['industry'] == 2) {
            $industry = '商超便利';
        } elseif ($data['industry'] == 3) {
            $industry = '购物中心';
        } elseif ($data['industry'] == 4) {
            $industry = '生鲜烘培';
        } elseif ($data['industry'] == 5) {
            $industry = '医药连锁';
        } elseif ($data['industry'] == 6) {
            $industry = '酒类连锁';
        } elseif ($data['industry'] == 7) {
            $industry = '其他';
        }

        //合作模式
        $client_type = '无';
        if ($data['client_type'] == 1) {
            $client_type = 'SAAS客户：租用';
        } elseif ($data['client_type'] == 2) {
            $client_type = '项目客户：买断';
        } elseif ($data['client_type'] == 3) {
            $client_type = '战略客户：长期定制';
        }

        $status = '已完成';
        if ($data['status'] == 2) {
            $status = '未完成';
        }

        $next_start_time = '无';
        if ($data['next_start_time'] == 1) {
            $next_start_time = '三天内';
        } else if ($data['next_start_time'] == 2) {
            $next_start_time = '一周内';
        } else if ($data['next_start_time'] == 3) {
            $next_start_time = '半个月内';
        } else if ($data['next_start_time'] == 4) {
            $next_start_time = '一个月内';
        } else if ($data['next_start_time'] == 5) {
            $next_start_time = '一个月以上';
        }

        //所购商品
        $product_id_string = [];
        if (!empty($data['product'])) {

            $data['product'] = json_decode($data['product'], true);
            foreach ($data['product']['id'] as $id) {
                if (isset($product[$id])) {
                    $product_id_string[] = $product[$id];
                }
            }
            if (!empty($data['product']['other'])) {
                $product_id_string[] = $data['product']['other'];
            }
        }
        $product_data = !empty($data['product']) ? implode(',', $product_id_string) : '';

        //商机预审
        if ($data['opportunity_pretrial'] == 1) {
            $opportunity_pretrial = '重要';
        } elseif ($data['opportunity_pretrial'] == 2) {
            $opportunity_pretrial = '一般';
        } else {
            $opportunity_pretrial = '无效';
        }

        $company_communication = CompanyCommunication::select('id', 'problem_type', 'custom_problem', 'support', 'other_support')
            ->where(['company_id' => $data['company_id']])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        $problem_type = '';
        $support = '';

        if (!empty($company_communication)) {

            foreach ($company_communication as $communication) {

                $problem_type = '无法接触到关键决策人';
                if ($communication['problem_type'] == 2) {
                    $problem_type = '项目延期或有变化';
                } elseif ($communication['problem_type'] == 3) {
                    $problem_type = '联系人变更或决策流程变更';
                } elseif ($communication['problem_type'] == 4) {
                    $problem_type = '客户需求不明确';
                } elseif ($communication['problem_type'] == 5) {
                    $problem_type = '竞争对手因素';
                } elseif ($communication['problem_type'] == 6) {
                    $problem_type = '需求无法确认';
                } elseif ($communication['problem_type'] == 7) {
                    $problem_type = '价格因素';
                } elseif ($communication['problem_type'] == 8) {
                    $problem_type = '合同条款争议';
                } elseif ($communication['problem_type'] == 9) {
                    $problem_type = '自定义问题:' . $communication['custom_problem'];
                }

                $support = '解决方案';
                if ($communication['support'] == 2) {
                    $support = '报价清单';
                } elseif ($communication['support'] == 3) {
                    $support = '系统演示';
                } elseif ($communication['support'] == 4) {
                    $support = '测试账号';
                } elseif ($communication['support'] == 5) {
                    $support = '现场拜访';
                } elseif ($communication['support'] == 6) {
                    $support = '其他支持:' . $communication['other_support'];
                }

                array_push($cellData, array($data['company_id'], $data['company_name'], $opportunity_pretrial, $client_stage, $industry, $data['account_manager'], $next_start_time, $status, $product_data, $problem_type, $support));
            }
        } else {

            array_push($cellData, array($data['company_id'], $data['company_name'], $opportunity_pretrial, $client_stage, $industry, $data['account_manager'], $next_start_time, $status, $product_data, $problem_type, $support));
        }

        //设置列宽
        $row_width = [
            'A' => 10,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20
        ];

        return [$cellData, $row_width];
    }

}
