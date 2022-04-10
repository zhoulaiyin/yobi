<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Models\Notice\Notice;

use App\Http\Models\Notice\SystemNotice;

use App\Http\Models\Notice\SystemNoticeEmail;

use App\Http\Controllers\Controller;

use App\Service\Wx\WxQyService;

class NoticeController extends Controller
{

    /**
     * 查询通知信息列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {

        $where = [];
        $notice_type = $request->input('notice_type');

        if (ebsig_is_int($notice_type)) {
            $where[] = ['notice_type', $notice_type];
        }

        $notice = Notice::select('id', 'title', 'remark', 'created_at')->where($where)->orderBy('id', 'DESC')->paginate($request->input('limit'))->toArray();
        if (!$notice || $notice['total'] == 0) {
            return response()->json(['code' => 100001, 'message' => '没有最新消息']);
        }

        return response()->json(['code' => 200, 'message' => 'OK', 'data' => [
            'total' => $notice['total'],
            'current_page' => $notice['current_page'],
            'last_page' => $notice['last_page'],
            'list' => $notice['data']
        ]]);

    }

    /**
     * 查询通知详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {

        $id = $request->input('id');
        if (empty($id)) {
            return response()->json(['code' => 100001, 'message' => '缺少参数：id']);
        }

        $notice = Notice::select('content', 'title', 'created_at')->find($id);
        if (empty($notice)) {
            return response()->json(['code' => 100002, 'message' => '通知信息不存在']);
        }

        return response()->json(['code' => 200, 'message' => 'OK', 'data' => $notice]);

    }


    /**
     * 邮件通知保存
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {

        $notice_data = $request->input('notice'); //系统通知信息
        $project_id = $request->input('project_id'); //项目ID
        $user = $request->input('user'); //成员ID数组
        $party = $request->input('party'); //部门ID数组


        //验证传值完整性及合法性
        if (empty($notice_data)) {
            return response()->json(['code' => 100001, 'message' => '系统通知信息不能为空']);
        }

        if (empty($user) || empty($party)) {
            return response()->json(['code' => 100002, 'message' => '部门ID数组(user),部门ID数组(party)两者至少传其一']);
        } else if ((!empty($user)) && (!is_array($user))) {
            return response()->json(['code' => 100003, 'message' => '成员ID请传数组格式']);
        } else if ((!empty($party)) && (!is_array($party))) {
            return response()->json(['code' => 100004, 'message' => '部门ID请传数组格式']);
        }


        $notice_data = json_decode($notice_data, true);
        foreach ($notice_data as $value) {

            $notice_obj = SystemNotice::where(['title' => $value['title'], 'project_id' => $value['project_id'], 'content' => $value['content']])->first();
            if ($notice_obj) {
                continue;
            }

            //存储“邮件通知”至数据库
            $notice = new SystemNotice();
            $notice->updated_at = Carbon::now();
            $notice->created_at = Carbon::now();
            $notice->content = $value['content'];
            $notice->send_status = $value['send_status'];
            $notice->project_id = $value['project_id'];
            $notice->title = $value['title'];
            $notice->save();

            //发送邮件
            $url = '/message/system-notice/' . $notice->id;
            $wx_qy_service = new WxQyService();
            $result = $wx_qy_service->sendTextCard('邮件通知', $value['content'], $url, $user, $party);
            $result = json_decode($result, true);
            if ($result['code'] != 200) {
                return response()->json(['code' => 100005, 'message' => '发送邮件通知失败：' . $result['message']]);
            }
            //更改发送状态
            $notice = SystemNotice::find($notice->id);
            $notice->send_status = 1;
            $notice->save();

        }

        return response()->json(['code' => 200, 'message' => 'OK']);


    }

}