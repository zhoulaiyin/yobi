<?php

namespace App\Http\Controllers\Api\Webi;

use App\Http\Controllers\Controller;
use App\Models\Classes\Control\ProjectClass;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Webi注册接口
     */
    public function register(Request $request)
    {
        //用户登录ID
        $userId = $request->input('user_id');
        //登录密码
        $password = $request->input('user_pwd');
        //用户名称
        $true_name = $request->input('true_name');
        //项目ID
        $project_id = $request->input('project_id');
        $project_name = $request->input('project_name');
        $domain_name = $request->input('domain_name');

        //验证单 过滤空值
        if (!isset($userId) || empty($userId)) {
            return response()->json([
                'code' => 100001,
                'message' => '账号不能为空'
            ]);
        }

        //登录密码不能为空
        if (!isset($password) || empty($password)) {
            return response()->json([
                'code' => 100002,
                'message' => '密码不能为空'
            ]);
        }

        //用户名称
        if (!isset($true_name) || empty($true_name)) {
            return response()->json([
                'code' => 100004,
                'message' => '用户名称不能为空'
            ]);
        }

        //判断密码格式
        if (isset($password) && !is_pwd($password)) {
            return response()->json([
                'code' => 100005,
                'message' => '密码格式不正确'
            ]);
        }

        //用户名称
        if (!isset($project_name) || empty($project_name)) {
            return response()->json([
                'code' => 100005,
                'message' => '项目名称不能为空'
            ]);
        }

        //用户名称
        if (!isset($domain_name) || empty($domain_name)) {
            return response()->json([
                'code' => 100006,
                'message' => '项目域名不能为空'
            ]);
        }

        if (!isset($project_id) || empty($project_id)) {
            $project_id = 0;
        } else {
            $Project = ProjectClass::fetch($project_id);
            $Project = json_encode($Project);
            if (!$Project) {
                $Project = new Project();
                $Project->project_id = $project_id;
                $Project->project_name = $project_name;
                $Project->project_domain_name = $domain_name;

                $Project->save();

                $msg = '新增项目成功';
            }
        }


        $User = Webiuser::find($userId);
        if ($User) {
            return response()->json([
                'code' => 100007,
                'message' => '此用户ID已存在'
            ]);
        }

        $Performance = new Webiuser();
        $Performance->creator = 'api';
        $Performance->create_at = Carbon::now();//创建时间
        $Performance->user_id = $userId;

        $Performance->update_at = Carbon::now();
        $Performance->true_name = $true_name;
        $Performance->project_id = $project_id;
        $Performance->user_pwd = md5($password);
        $Performance->project_name = $project_name;
        $Performance->useFlg = 1;
        if (!empty($request->input('mobile'))) {
            $Performance->mobile = $request->input('mobile');
        }
        if (!empty($request->input('email'))) {
            $Performance->email = $request->input('email');
        }

        try {
            $Performance->save();
        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $e->getMessage()
            );
        }

        return response()->json(['code' => 200, 'message' => '注册成功' . $msg]);
    }

}