<?php

namespace App\Http\Controllers\Admin;

use DB;

use App;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis as Redis;

use App\Models\Classes\Console\System\UserClass;
use App\Models\Classes\Console\System\PermissionClass;
use App\Models\Classes\Console\System\PermissionGroupClass;


class AdminController extends Controller
{

    /**
     * @param Request $request 请求对象
     * @param int $dashboard 主菜单标识
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function main(Request $request, $dashboard = 0)
    {

        $sidebar = $request->input('sidebar');
        //用户名
        $userId = Redis::get('ADMIN_USER_ID' . session()->getId());
        //查询用户信息
        $where = [['user_id', $userId]];
        $limit = [
            'pageSize' => '1'
        ];
        $user = UserClass::getList($where, $limit);


        //用户信息
        $data['user_id'] = $userId;
        $data['user_name'] = $user->user_name;
        $data['mobile'] = $user->mobile;

        //查询用户角色权限组信息
        $role_permission_group = RolePermission::where('role_id', $user->role_id)->groupBy('permission_group_id')->get();

        if ($role_permission_group) {
            $role_permission_group = $role_permission_group->pluck('permission_group_id')->toArray();
        } else {
            $role_permission_group = [];
        }

        $default_url = '';

        //首页链接
        $index_group = [
            'id' => 0,
            'name' => '首页',
            'icon' => '/images/admin/index.png',
            'active' => 0
        ];
        if ($dashboard == 0) {
            $index_group['icon'] = '/images/admin/index-active.png';
            $index_group['active'] = 1;
            $default_url = 'eoa/index';
        }
        $data['permission_group'][] = $index_group;

        //查询权限组信息
        $where[] = [];
        $limit = [
            'pageSize' => 1,
            'orderBy' => 'sort',
            'sort' => 'ASC'
        ];
        $permission_group = PermissionGroupClass::getList($where, $limit);

        if ($permission_group) {

            foreach ($permission_group as &$group) {

                if (!in_array($group['id'], $role_permission_group)) {
                    continue;
                }

                if ($dashboard == $group['id']) {
                    $active = 1;
                    $icon = $group['active_icon'];
                    $data['dashboard'] = $group['group_name'];
                } else {
                    $active = 0;
                    $icon = $group['icon'];
                }

                $data['permission_group'][] = [
                    'id' => $group['id'],
                    'name' => $group['short_name'],
                    'icon' => $icon,
                    'active' => $active
                ];
            }

        }

        if ($dashboard != 0) {
            //根据选中的权限组id查询对应的角色权限信息
            $user_role_permission = RolePermission::select('permission_id')->where(['role_id' => $user->role_id, 'permission_group_id' => $dashboard])->get()->toArray();

            foreach ($user_role_permission as &$role_permission) {

                $permission = PermissionClass::fetch($role_permission['permission_id']);
                $permission = json_encode($permission);

                if (!$permission || $permission->permissio_type == 2) {
                    continue;
                }

                $active = 0;
                if (!empty($sidebar) && strpos($sidebar, $permission->permissio_request) !== false) {
                    $default_url = $sidebar;
                    $active = 1;
                }

                $data['menus'][] = [
                    'name' => $permission->short_name,
                    'link' => $permission->permission_request,
                    'active' => $active
                ];

            }

            if (empty($default_url)) {
                $default_url = $data['menus'][0]['link'];
                $data['menus'][0]['active'] = 1;
            }

        }

        if (strpos($default_url, '/') !== 0) {
            $default_url = '/' . $default_url;
        }

        $data['default_url'] = $default_url;
        $data['pwdFlg'] = $user->pwd_flg;
        return view('admin/main', $data);

    }

    //首页
    public function index()
    {
        $data = [];
        //  获取当前用户
        $userId = Redis::get('ADMIN_USER_ID' . session()->getId());
        // 待我处理的需求
        $data['demands'] = Demand::where('person_in_charge_id', $userId)
            ->leftJoin("user_master as um", "demand_master_new.creator", '=', 'um.userID')
            ->orderBy('demand_id', 'DESC')
            ->limit(5)
            ->get(['demand_id', 'trueName', 'demand_name', 'demand_status'])
            ->toArray();

        // 我提出的需求
        //暂定
        $data['demand_puts'] = Demand::where('demand_master_new.creator', $userId)
            ->leftJoin("user_master as um", "demand_master_new.creator", '=', 'um.userID')
            ->orderBy('demand_id', 'DESC')
            ->limit(5)
            ->get(['demand_id', 'demand_name', 'trueName', 'demand_status'])
            ->toArray();
        //  待我处理的任务
        $data['tasks'] = Task::where('development_user_id', $userId)
            ->leftJoin("user_master as um", "task.creator", '=', 'um.userID')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get(['id', 'trueName', 'task_name', 'task_status'])
            ->toArray();

        // 由我提出的任务
        $data['task_puts'] = Task::where('task.creator', $userId)
            ->leftJoin("user_master as um", "task.creator", '=', 'um.userID')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get(['id', 'trueName', 'task_name', 'task_status'])
            ->toArray();

        //我的任务BUG
        $data['my_problems'] = Task::where('task.development_user_id', $userId)
            ->join('bug_master as bm', 'bm.task_id', '=', 'task.id')
            ->join('user_master as um', 'um.userID', '=', 'task.creator')
            ->distinct()
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get(['task.id', 'trueName', 'task_name', 'task_status'])
            ->toArray();

        $data['department_problems'] = [];
        $data = $this->makeData($data);

        $data['demand_status'] = [
            1 => '待确认',
            2 => '待审核',
            3 => '原型设计',
            4 => 'UI设计',
            5 => '待开发',
            6 => '开发中',
            7 => '待测试',
            8 => '待发布',
            9 => '已完成',
            10 => '已取消',
            '-' => '-'
        ];
        $data['task_status'] = [
            1 => '待分派',
            2 => '待接收',
            3 => '进行中',
            4 => '待审核',
            5 => '已完成',
            6 => '已取消',
            7 => '已拒绝',
            '-' => '-'
        ];
        $data['plan_state'] = [
            1 => '未确认',
            2 => '已确认',
            3 => '已完成',
            4 => '已取消',
            '-' => '-'
        ];

        // 通知列表
        //暂定
        $data['notices'] = Notice::select('id', "title", 'content', "video_link", "visit_num")
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()
            ->toArray();
        foreach ($data['notices'] as &$notice) {
            $notice['visited'] = 0;
            if (NoticeVisit::where(['notice_id' => $notice['id'], 'userID' => $userId])->first()) {
                $notice['visited'] = 1;
            }
        }


        return view('admin/index', $data);

    }

    //扎扎APP
    public function zaza()
    {
        $data['zaza'] = '/images/home/common/zhazha.png';
        return view('admin/zaza', $data);
    }

    private function makeData($data)
    {
        foreach ($data as $key => &$dt) {
            //截取字符串（最大显示16个字节，以保证前端不乱码）
            if (in_array($key, ['demands', 'demand_puts'])) {
                foreach ($dt as &$vv) {

                    if (mb_strlen($vv['demand_name'], 'utf-8') > 17) {
                        $vv['demand_name_short'] = mb_substr($vv['demand_name'], 0, 17, 'utf-8') . '…';
                    } else {
                        $vv['demand_name_short'] = $vv['demand_name'];
                    }
                }

            } else if (in_array($key, ['tasks', 'task_puts', 'my_problems'])) {
                foreach ($dt as &$vv) {

                    if (mb_strlen($vv['task_name'], 'utf-8') > 17) {
                        $vv['task_name_short'] = mb_substr($vv['task_name'], 0, 17, 'utf-8') . '…';
                    } else {
                        $vv['task_name_short'] = $vv['task_name'];
                    }
                }
            } else if (in_array($key, ['plan', 'plan_puts'])) {

                foreach ($dt as &$vv) {

                    if (mb_strlen($vv['plan_name'], 'utf-8') > 17) {
                        $vv['plan_name_short'] = mb_substr($vv['plan_name'], 0, 17, 'utf-8') . '…';
                    } else {
                        $vv['plan_name_short'] = $vv['plan_name'];
                    }
                }
            }

            if (count($dt) >= 5) {
                continue;
            }
            if (count($dt) > 0) {
                if (in_array($key, ['demands', 'demand_puts'])) {

                    $dt[] = [
                        'demand_id' => '-',
                        'trueName' => '-',
                        'demand_name' => '-',
                        'demand_status' => '-'
                    ];
                } else if (in_array($key, ['tasks', 'task_puts', 'my_problems'])) {

                    $dt[] = [
                        'task_id' => '-',
                        'trueName' => '-',
                        'task_name' => '-',
                        'task_status' => '-'
                    ];
                } else if (in_array($key, ['plan', 'plan_puts'])) {
                    $dt[] = [
                        'id' => '-',
                        'trueName' => '-',
                        'plan_name' => '-',
                        'plan_state' => '-'
                    ];
                }

                $data = $this->makeData($data);
            }
        }
        return $data;

    }

    /*
     * 通知消息 查看更多
     */
    public function noticeMore()
    {

        return view('admin/more');
    }

    /*
     * 通知消息 查看更多时请求的ajax方法
     */
    public function noticeMoreAjax(Request $request)
    {
        $notices = Notice::orderBy($request->input('sort'), $request->input('order'))
            ->paginate($request->input('limit'))
            ->toArray();
        //返回数组
        $res_data = array(
            'total' => $notices['total'],
            'rows' => array()
        );

        foreach ($notices['data'] as &$row) {

            if (trim($row['notice_type']) == 1) {
                $row['notice_type'] = '内部通知';
            }

            $res_data['rows'][] = array(
                'title' => '<a href="javascript:;" onclick="more.detail(' . $row['id'] . ')">' . $row['title'] . '</a>',
                'creator' => $row['creator'],
                'created_at' => $row['created_at']
            );
        }

        return response()->json($res_data);
    }

    //  通知消息详情
    public function getMessage($id)
    {
        // 验证单
        if (!isset($id) && empty($id)) {
            return response()->json([
                'code' => 100001,
                'message' => '缺少参数通知ID'
            ]);
        }

        if (!$notice = Notice::find($id)) {
            return response()->json([
                'code' => 100002,
                'message' => '没有这条通知'
            ]);
        }
        //  当前用户
        $userId = Redis::get('ADMIN_USER_ID' . session()->getId());
        // 开始事务
        try {

            DB::beginTransaction();

            // 更改访问次数
            $notice->visit_num++;
            $notice->save();

            //  插入访问数据
            if (!$notice_visit = NoticeVisit::where(['notice_id' => $id, 'userID' => $userId])->first()) {     // 新增
                $notice_visit = new NoticeVisit();
                $notice_visit->uuid = makeUuid();
                $notice_visit->created_at = Carbon::now();
                $notice_visit->creator = $userId;

                $notice_visit->notice_id = $id;
                $notice_visit->project_id = $id;
                $notice_visit->userID = $userId;
            } else {  // 修改

                $notice_visit = NoticeVisit::where(['notice_id' => $id, 'userID' => $userId])->first();
            }
            $notice_visit->updated_at = Carbon::now();   // 最后一次访问时间
            $notice_visit->save();

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);

        }
        $res_data = [
            'id' => $id,
            'title' => $notice['title'],
            'content' => "<div style='padding:20px 30px;'>" . htmlspecialchars_decode($notice['content']) . "</div>"
        ];
        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'data' => $res_data
        ]);

    }

}
