<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Classes\Console\System\PermissionClass;
use App\Models\Classes\Console\System\PermissionGroupClass;
use Illuminate\Support\Facades\Redis as Redis;
use DB;

class WeBIController extends Controller
{

    public function main($groupID = 0)
    {

        //查询一级分组
        $where = [['parent_id', '=', "0"]];
        $first_group = PermissionGroupClass::getList($where);

        if (empty($groupID)) {
            $groupID = $first_group['data'][0]['id'];
        }
        $first_menu = [];
        $global_menu = [];
        if ($first_group['count'] > 0) {
            foreach ($first_group['data'] as $group) {

                //查询二级权限
                $where = ['parent_group_id' => $group['_id'], 'permission_type' => 1];
                $second_group = PermissionClass::getList($where);

                if (!$second_group['count'] > 0) {
                    continue;
                }

                $second_menu = [];
                foreach ($second_group['data'] as $twoGroup) {

                    //查询三级权限
                    $where = ['group_id' => $twoGroup['_id'], 'permission_type' => 1];
                    $third_menus = PermissionClass::getList($where);

                    $list = [];
                    if (!empty($third_menus)) {
                        foreach ($third_menus['data'] as &$p) {
                            $list[] = [
                                'id' => $p['permission_id'],
                                'name' => $p['permission_name'],
                                'url' => '/' . $p['permission_url']
                            ];
                        }
                    }

                    if (empty($list) && empty($group['url'])) {
                        continue;
                    }

                    $second_menu[] = [
                        'id' => $twoGroup['_id'],
                        'name' => $twoGroup['permission_name'],
                        'url' => empty($twoGroup['permission_url']) ? '' : '/' . $twoGroup['permission_url'],
                        'list' => $list
                    ];

                }

                if (!empty($second_menu)) {
                    $first_menu[$group['id']] = $group['group_name'];
                    $global_menu[$group['id']] = $second_menu;
                }

            }
        }

        $data = [
            'select_group_id' => $groupID,
            'first_group' => $first_menu,
            'menu' => $global_menu[$groupID],
            'user_name' => UserName()
        ];

        return view('admin/main1', $data);

    }

    //空白页
    public function index()
    {
        return view('blank');
    }

    //重载权限
    public function reloadPermission()
    {

        //查询用户角色信息
        $user_role = UserRole::where('user_id', UserId())->get();
        if (empty($user_role)) {
            return ['code' => 400, 'msg' => '没有找到员工部门信息'];
        }
        $user_role = $user_role->pluck('role_id')->toArray();

        //待定
        $role_permission = DB::table('role_permission')
            ->join('permission', 'role_permission.permission_id', '=', 'permission.permission_id')
            ->select('permission.permission_id', 'permission.permission_url')
            ->whereIn('role_id', $user_role)
            ->get()
            ->toArray();

        if (empty($role_permission)) {
            return ['code' => 400, 'msg' => '没有找到员工权限信息'];
        }
        //待定
        $Role_Group = DB::table('role as r')
            ->select('g.group_code', 'g.cy_flg')
            ->leftjoin('role_group as g', 'r.group_id', '=', 'g.id')
            ->where('r.id', '=', $user_role[0])
            ->get()->toArray();

        $session_permission = [];
        foreach ($role_permission as $p) {
            $session_permission[$p->permission_id] = $p->permission_url;
        }

        $session_id = session()->getId();

        $login_session = json_decode(Redis::get('LOGIN_SESSION' . $session_id), true);

        //获取剩余登录时效
        $session_time = Redis::ttl('LOGIN_SESSION' . $session_id);

        $login_session['USER_PERMISSIONS'] = $session_permission;
        $login_session['USER_ROLEID'] = $user_role[0];
        $login_session['USER_ROLE_GROUP_CODE'] = $Role_Group[0]->group_code;
        $LOGIN_SESSION['USER_ROLE_GROUP_TYPE'] = $Role_Group[0]->cy_flg;

        Redis::setex('LOGIN_SESSION' . $session_id, $session_time, json_encode($login_session));

        Redis::del('PS_PERMISSIONEDURI' . $session_id);

        return ['code' => 200, 'msg' => 'ok'];

    }

}



