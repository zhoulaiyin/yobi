<?php

namespace App\Http\Controllers\WeBI\backend;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\BiColorClass;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis as Redis;

class ColorController extends Controller
{
    //图表颜色列表页
    public function index()
    {

        return view('webi/backend/biAttribute/color/list');

    }

    public function search()
    {

        $limit = [
            'pageSize' => 999,
            'orderBy' => 'color_code',
            'sort' => 'ASC'
        ];
        $column = 'color_code';
        $master = BiColorClass::getList([], $limit, $column);

        //返回数组
        $return_data = [
            'code' => 0,
            'msg' => '',
            'count' => isset($master['count']) ? $master['count'] : 0,
            'data' => array()
        ];

        if ($master['count'] > 0) {

            foreach ($master['data'] as $row) {

                $return_data['data'][] = [
                    'color_id' => $row['_id'],
                    'color_code' => $row['color_code']
                ];
            }
        }

        return response()->json(['code' => 200, 'master' => $return_data]);
    }

    //图表颜色新增
    public function add(Request $request)
    {

        $bi_title = substr($request->input('title'), 1, 6);

        if (!isset($bi_title) || empty($bi_title)) {
            return response()->json(array('code' => 100001, 'message' => '颜色代码不能为空'));
        }

        $where = ['color_code' => $bi_title];
        $color = BiColorClass::getList($where);

        if ($color['count']) {
            return response()->json(array('code' => 100002, 'message' => '该颜色已存在!'));
        }

        $_id = BiColorClass::save([
            'color_code' => $bi_title
        ]);

        BiColorClass::save([
            'color_id' => $_id['data']
        ], $_id['data']);

        $prj_data = [
            0 => [
                '_id' => $_id['data'],
                'color_id' => $_id['data'],
                'color_code' => $bi_title,
            ]
        ];

        $data['master'] = $prj_data;

        return response()->json(['code' => 200, 'message' => $data]);

    }

    //图表颜色删除
    public function del($bi_id)
    {

        BiColorClass::del($bi_id);

        return response()->json(['code' => 200, 'message' => "删除成功"]);

    }

    //图表颜色编辑
    public function edit(Request $request)
    {

        $bi_title = substr($request->input('title'), 1, 6);
        $_id = $request->input('_id');

        if (empty($bi_title)) {
            return response()->json(array('code' => 10002, 'message' => '请输入图表颜色代码'));
        }

        $statusGroup = BiColorClass::fetch($_id);
        if (!$statusGroup) {
            return response()->json(['code' => 400, 'message' => '该颜色不存在，请刷新页面！']);
        }

        $where = ['color_code' => $bi_title];
        $color = BiColorClass::getList($where);

        if ($color['count']) {
            return response()->json(array('code' => 100002, 'message' => '该颜色已存在!'));
        }

        BiColorClass::save([
            'color_code' => $bi_title,
            'color_id' => $_id
        ], $_id);

        return response()->json(['code' => 200, 'message' => "保存成功！", 'title' => $bi_title]);
    }
}
