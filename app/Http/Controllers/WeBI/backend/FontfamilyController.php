<?php
namespace App\Http\Controllers\WeBI\backend;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\BiFontFamilyClass;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis as Redis;

class FontfamilyController extends Controller
{

    public function index() {

        return view('webi/backend/biAttribute/fontfamily/list');

    }

    public function search (Request $request){

        //页面数据
        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'sort_order',
            'sort' => 'ASC'
        ];
        $column = 'font_id, val, sort_order';
        $group_data = BiFontFamilyClass::getList([], $limit, $column);

        $return_data = array(
            'code' => 0,
            'msg'  => '',
            'count' => isset($group_data['count']) ? $group_data['count'] : 0,
            'data' => array()
        );

        if ($group_data['count'] > 0) {
            foreach ($group_data['data'] as $data) {
                $return_data['data'][] = array(
                    'font_id' => $data['_id'],
                    'fontfamily' => $data['val'],
                    'sort_order' => $data['sort_order']
                );

            }
        }

        return $return_data;
    }

    public function add(Request $request) {

        $fontfamily = $request->input('title');
        $sort_order = $request->input('sort_order');


        if (!isset($fontfamily) || empty($fontfamily)) {
            return response()->json(array('code' => 100001, 'message' => '字体名不能为空'));
        }
        if (!isset($sort_order) || empty($sort_order)) {
            return response()->json(array('code' => 100001, 'message' => '排序值不能为空'));
        }

        $where = [['val', $fontfamily]];
        $font = BiFontFamilyClass::getList($where);
        if($font['count']){
            return response()->json(array('code' => 100002, 'message' => '该字体已存在!'));
        }

        $where = [['sort_order', (int)$sort_order]];
        $sort = BiFontFamilyClass::getList($where);

        if($sort['count']){
            return response()->json(array('code' => 100002, 'message' => '该排序值已存在!'));
        }

        BiFontFamilyClass::save([
            'val' => $fontfamily,
            'sort_order' => (int)$sort_order
        ]);

        return response()->json([ 'code' => 200, 'message' => 'ok' ]);

    }

    public function del($font_id)
    {

        BiFontFamilyClass::del($font_id);
        return response()->json(['code' => 200, 'message' => "删除成功"]);

    }

    public function edit(Request $request){
        $fontfamily = $request->input('title');
        $sort_order = $request->input('sort_order');
        $font_id = $request->input('font_id');

        if (empty($fontfamily)) {
            return response()->json(array('code' => 10002, 'message' => '请输入字体名称'));
        }
        if (empty($sort_order)) {
            return response()->json(array('code' => 10002, 'message' => '请输入排序值'));
        }

        $statusGroup = BiFontFamilyClass::fetch($font_id);
        if(!$statusGroup){
            return response()->json([ 'code' => 400 , 'message' => '该字体不存在，请刷新页面！' ]);
        }

        $where = [['val', $fontfamily], ['font_id', '!=', $font_id]];
        $font = BiFontFamilyClass::getList($where);

        if($font['count']){
            return response()->json( array('code' => 100002, 'message' => '该字体已存在!') );
        }

        $where = [['sort_order', (int)$sort_order], ['font_id', '!=', $font_id]];
        $sort = BiFontFamilyClass::getList($where);
        if($sort['count']){
            return response()->json(array('code' => 100002, 'message' => '该排序值已存在!') );
        }

        BiFontFamilyClass::save([
            'val' => $fontfamily,
            'sort_order' => (int)$sort_order
        ], $font_id);

        return response()->json([ 'code' => 200 , 'message' => "保存成功！", 'title' => $fontfamily ]);

    }

    public function save(Request $request) {

        $args = $request->all();

        $BiFontFamily = BiFontFamilyClass::fetch($args['font_id']);
        if (!$BiFontFamily) {
            return response()->json(['code' => 10001, 'message' => '更新失败,字体不存在!']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['font_id', '!=', $args['font_id']]];
        $column = 'sort_order';
        $order_data = BiFontFamilyClass::getList($where, [], $column);

        if($order_data){
            return response()->json(['code' => 10002, 'message' => '更新失败,此排序值已存在!']);
        }

        if( !empty($args['sort_order']) ){
            $BiFontFamily['sort_order'] = $args['sort_order'];
        }

        $BiFontFamily->save();

        return response()->json(['code'=> 200, 'message' => '更新成功']);
    }
}
