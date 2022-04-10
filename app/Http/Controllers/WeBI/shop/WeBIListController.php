<?php

namespace App\Http\Controllers\WeBI\shop;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Classes\Control\BiUserClass;
use App\Models\Classes\Control\Statement\BiGroupClass;
use App\Models\Classes\Control\Statement\BiMasterClass;
use App\Models\Classes\Control\Statement\BiMasterModuleClass;
use App\Models\Classes\Control\DataSource\BiViewClass;

class WeBIListController extends Controller
{
	
    //首页
    public function index() {
        global $WS;

        $user_data = BiUserClass::fetch($WS->shopUserID);

        //页面数据
        $data = [];
        $group_title = [];

        $limit = [
            'pageSize' => 999,
            'orderBy' => 'bi_user_id',
            'sort' => 'ASC',
        ];

        $groupResult = BiGroupClass::getList(['bi_user_id' => $WS->mainUserID], $limit, 'group_id,group_name');
        if($groupResult['count'] > 0){
            $data['group'] = $groupResult['data'];
            foreach ($groupResult['data'] as $p) {
                array_push($group_title, $p['group_name']);
            }
        }

        switch ($WS->userRole) {
            case 1:
                $data['role'] = '总部';
                break;

            case 2:
                $data['role'] = '分部';
                break;

            case 3:
                $data['role'] = '门店';
                break;

            default:
                $data['role'] = '';
        }

        $data['is_admin'] = false;
        if ($WS->shopUserID == $WS->mainUserID || BiUserClass::isAdmin()) {
            $data['is_admin'] = true;
        }

        $user_permission = empty($user_data['user_permission']) ? [] : explode(',', $user_data['user_permission']);
        $data['btn'] = false;

        if (count($user_permission) && !empty(array_intersect([1,2], $user_permission))) {
            $data['btn'] = true;
        }

        $data['group_id'] = empty($data['group']) ? 0 : $data['group'][0]['_id'];
        $data['head_pic'] = $user_data['head_pic'];
        $data['userName'] = $WS->shopCustID;
        $data['group_already'] = json_encode($group_title);

        return view('webi/shop/listbi', $data);
    }

    /**
     * 新建&编辑分组
     * @param Request $request
     */
    public function editGroup(Request $request) {
        $title = $request->input('title', '');
        $group_id = $request->input('group_id', '');

        if (empty($title)){
            return response()->json(['code' => 10000 , 'message' => '请输入分组名称']);
        }

        if (!empty($group_id)){
            $BiGroup = BiGroupClass::fetch($group_id);
            if(empty($BiGroup)){
                return response()->json(['code' => 400 , 'message' => '分组不存在，请刷新页面！']);
            }
        }

        $save_dt = [
            'group_name' => $title
        ];

        $response = BiGroupClass::save($save_dt, $group_id);

        return response()->json($response);
    }

    /**
     * 删除分组
     * @param Request $request
     */
    public function delGroup(Request $request) {
        global $WS;

        $group_id = $request->input('group_id', '');
        $target_id = $request->input('target_id', '');

        if (empty($group_id)){
            return response()->json([ 'code' => 10000 , 'message' => '缺失分组信息' ]);
        }

        $BiGroup = BiGroupClass::fetch($group_id);
        if(empty($BiGroup)){
            return response()->json([ 'code' => 10003 , 'message' => '分组不存在，请刷新页面~' ]);
        }

        //查询当前分组下是否存在报表
        $where = [
            ['bi_user_id' , $WS->shopUserID],
            ['group_id', $group_id],
        ];
        $limit = [
            'pageSize' => 9999
        ];

        $bi_master = BiMasterClass::getList($where, $limit);
        if ($bi_master['count'] > 0) {
            if(empty($target_id)){
                return response()->json([ 'code' => 10000 , 'message' => '数据已过期，请刷新页面~' ]);
            }

            $targetGroup = BiGroupClass::fetch($target_id);
            if(empty($targetGroup)){
                return response()->json([ 'code' => 10003 , 'message' => '目标分组不存在，请刷新页面~' ]);
            }
        }

        try {

            DB::beginTransaction();

            BiGroupClass::del($group_id);

            if($bi_master['count'] > 0){
                BiMasterClass::save(['group_id' => $target_id], $group_id);
            }

            //提交事务
            DB::commit();
        }catch (\Exception $e) {
            //回滚
            DB::rollBack();
            return response()->json(['code' => 500 ,'message' => "删除失败"]);
        }

        return response()->json(['code' => 200 ,'message' => "删除成功"]);
    }

    /**
     * 获取除编辑分组的其他所有分组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function globalGroup($group_id) {
        global $WS;

        $BiGroup = BiGroupClass::fetch($group_id);
        if(empty($BiGroup)){
            return response()->json([ 'code' => 404 , 'message' => '分组不存在' ]);
        }

        $groupWhere = [
            [ 'bi_user_id', '=', $WS->mainUserID ],
            [ '_id', '!=', $group_id]
        ];
        $limit = [
            'pageSize' => 9999,
            'sort' => 'ASC'
        ];
        $groupResult = BiGroupClass::getList($groupWhere, $limit, '_id,group_name');


        $masterWhere = [
            [ 'bi_user_id', '=', $WS->mainUserID ],
            [ 'group_id', $group_id]
        ];
        $masterResult = BiMasterClass::getList($masterWhere, ['pageSize' => 9999]);

        return response()->json([
            'code' => 200 ,
            'message' => 'ok',
            'group' => $groupResult['data'],
            'bi_num' => $masterResult['count']
        ]);
    }

    /**
     * 新建报表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTable(Request $request) {
        global $WS;

        $group_id = $request->input('group_id', '');
        $bi_title = $request->input('title', '');

        if (empty($group_id)) {
            return response()->json(array('code' => 100001,'message' => '参数错误'));
        }

        if (empty($bi_title)) {
            return response()->json(array('code' => 100001,'message' => '报表名称不能为空'));
        }

        $uid = makeUuid();

        $attribute_json = array(
            'general' => array(
                'title' => $bi_title,
                'backgroundColor' => '',
                'backgroundImage' => '',
                'border' => '5',
                'border_image_slice' => '',
                'border_image_repeat' => 'repeat',
                'border_image_source' => '',
                'refresh_frequency' => ''
            )
        );

        $bi_id = "";

        $masterResult = BiMasterClass::save([
            'uid' => $uid,
            'bi_user_id' => $WS->mainUserID,
            'project_id' => $WS->projectID,
            'group_id' => $group_id,
            'bi_title' => $bi_title,
            'attribute_json' => json_encode($attribute_json)
        ]);

        if ($masterResult['code'] == 200) {
            $bi_id = $masterResult['data'];
        }

        $data = [
            'master' => [
                [
                    '_id' => $bi_id,
                    'bi_title' => $bi_title,
                    'uid' => $uid
                ]
            ]
        ];

        return response()->json([ 'code' => 200, 'message' => $data ]);
    }

    /**
     * 移动单张报表转至其他分组
     */
    public function moveSave(Request $request) {
        $bi_id = $request->input('bi_id', '');
        $group_id = $request->input('group_id', '');

        if (empty($bi_id) || empty($group_id)) {
            return response()->json([ 'code' => 10004 , 'message' => "参数错误" ]);
        }

        $BiMaster = BiMasterClass::fetch($bi_id);
        if (empty($BiMaster)) {
            return response()->json([ 'code' => 10003 , 'message' => '报表不存在，请刷新页面~' ]);
        }

        if ($group_id == $BiMaster['group_id']) {
            return response()->json(['code' => 10003 , 'message' => '报表不允许移动至自身分组']);
        }

        $BiGroup = BiGroupClass::fetch($group_id);
        if (empty($BiGroup)) {
            return response()->json(['code' => 10003 , 'message' => '分组不存在，请刷新页面~']);
        }

        BiMasterClass::save(['group_id' => $group_id], $bi_id);

        return response()->json([ 'code' => 200 , 'message' => "操作成功" ]);
    }

    /**
     * 删除项目
     */
    public function del($bi_id)
    {
        $master = BiMasterClass::fetch($bi_id);
        if (!$master) {
            return response()->json(['code' => 400 , 'message' => "报表已删除，请刷新"]);
        }

        $result = BiMasterClass::del($bi_id);

        $module = BiMasterModuleClass::getList(['bi_id' => $bi_id], ['pageSize' => 99]);
        if ($module['count'] > 0) {
            foreach ($module['data'] as $i => $dt) {
                BiMasterModuleClass::del($dt['_id']);
            }
        }

        return response()->json($result);
    }

    /**
     * 搜索报表
     * @param $info 搜索关键词
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($info)
    {
        global $WS;

        if (empty($info)) {
            return response()->json(['code' => 10000 , 'msg' => "请输入报表名称"]);
        }

        $returnDT = [];
        $where = [
            ['bi_user_id', '=', $WS->mainUserID],
            ['bi_title', 'like', '%'.trim($info).'%']
        ];

        $masterResult = BiMasterClass::getList($where, ['pageSize' => 9999], '_id,bi_title,group_id');
        if ($masterResult['count'] > 0) {
            foreach ($masterResult['data'] as $i => $dt) {
                $masterData = $dt;

                $group = BiGroupClass::get(['group_id' => $dt['group_id']]);
                $masterData['group_name'] = $group['group_name'];

                $returnDT[] = $masterData;
            }
        }

        return response()->json([ 'code' => 200 , 'data' => $returnDT ]);
    }

    /**
     * 查看分组下报表
     * @param $group_id 分组ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function BIMasterList($group_id){
        global $WS;

        if (empty($group_id)) {
            return response()->json([
                'code' => 400,
                'group_id'=> 0,
                'master'=>[],
                'message' => []
            ]);
        }

        $where = [
            ['bi_user_id', $WS->mainUserID],
            ['group_id', $group_id],
        ];
        $limit = ['pageSize' => 999];
        $title = [];

        $masteResult = BiMasterClass::getList($where, $limit, 'bi_id','bi_title','uid','group_id');
        if($masteResult['count'] > 0){
            foreach ($masteResult['data'] as $i => $p) {
                array_push($title, $p['bi_title']);
            }
        }

        return response()->json([
            'code' => 200,
            'group_id'=> $group_id,
            'master'=> $masteResult['data'],
            'message' => [
                'title_already' => json_encode($title)
            ]
        ]);
    }

    //复制报表
    public function copy(Request $request){
        global $WS;

        $data = [];
        $bi_id = $request->input('bi_id');

        $master_data = BiMasterClass::fetch($bi_id);
        if(!$master_data){
            return response()->json([ 'code' => 10001 , 'message' => "不存在此报表！" ]);
        }

        //插入报表数据
        $uid = makeUuid();

        $attribute_json = json_decode($master_data['attribute_json'],true);
        $attribute_json['uid'] = $uid;

        $saveData = [
            'bi_user_id' => $WS->mainUserID,
            'group_id' => $master_data['group_id'],
            'project_id' => $master_data['project_id'],
            'uid' => $uid,
            'bi_title' => $master_data['bi_title'],
            'attribute_json' => json_encode($attribute_json),
        ];

        $addResult = BiMasterClass::save($saveData);
        if ($addResult['code'] != 200) {
            return response()->json([ 'code' => $addResult['code'] , 'message' => $addResult['message'] ]);
        }


        $biId = $addResult['data'];
        $attribute_json['bi_id'] = $biId;

        BiMasterClass::save(['attribute_json' => json_encode($attribute_json)], $biId);

        $sql_data = [];
        $view_sql_data = [];

        $moduleResult = BiMasterModuleClass::getList(['bi_id' => $bi_id], ['pageSize' => 9999]);
        if ($moduleResult['count'] > 0) {
            foreach ($moduleResult['data'] AS $key => $value){
                $uuid = makeUuid();

                $BiView = BiViewClass::fetch($value['view_id']);
                $view_id = $value['view_id'];

                if ($BiView['source_id']) {
                    $view_id = generate_seqno('view_id');

                    //视图数据
                    $view_sql_data[] = [
                        'creator' =>  $WS->shopCustID,
                        'view_id' => $view_id,
                        'view_name' => $BiView['view_name'],
                        'bi_user_id' => $WS->mainUserID,
                        'group_id' => $BiView['group_id'],
                        'source_id' => $BiView['source_id'],
                        'project_id' => $BiView['project_id'],
                        'table_json' => $BiView['table_json']
                    ];
                }

                //BI数据
                $db_json = json_decode($value['db_json'], true);
                $db_json['view_id'] = $view_id; //修改复制的BI视图ID
                $value['db_json'] = json_encode($db_json);

                $sql_data[] =[
                    'creator' => $WS->shopCustID,
                    'uid' => $uuid,
                    'bi_id' => $biId,
                    'view_id' => $view_id,
                    'bi_json' => $value['bi_json'],
                    'db_json' => $value['db_json'],
                    'attribute_json' => $value['attribute_json'],
                    'chart_json' => $value['chart_json']
                ];
            }
        }

        try {

            DB::beginTransaction();

            if (!empty($view_sql_data)) {
                foreach ($view_sql_data as $i => $val) {
                    BiViewClass::save($val);
                }
            }

            if (!empty($sql_data)) {
                foreach ($sql_data as $i => $val) {
                    BiMasterModuleClass::save($val);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
        }

        $data['master'] = [
            [
                'bi_id'=>$biId,
                'bi_title'=>$master_data['bi_title'],
                'uid'=>$uid
            ]
        ];

        return response()->json([ 'code' => 200 , 'msg' => 'ok','data' => $data ]);
    }

}