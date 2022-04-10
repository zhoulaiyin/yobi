<?php

namespace App\Http\Controllers\Document;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Classes\Console\Document\DocOperateManualGroupClass;
use App\Models\Classes\Console\Document\DocOperateManualGroupItemClass;

class OperateManualController extends Controller
{

    public function index(){
        $limit = [
            'orderBy' => 'sort_order',
            'sort' => 'ASC',
            'pageSize' => '99'
        ];
        $third_group = DocOperateManualGroupClass::getList([], $limit);
        if (!$third_group['count']) {
            return redirect('error/doc?msg=文档不存在');
        }

        $data['group'] = [];
        foreach ($third_group['data'] as $key => $group) {
            $list = [];

            $third = DocOperateManualGroupItemClass::getList(['group_id' => $group['group_id']], $limit);

            if(!$third['count']) {
                continue;
            }

            if ($key==0) {
                $data['group_id'] = $group['group_id'];
                $data['default_id'] = $third['data'][0]['item_id'];
            }

            foreach ($third['data'] as &$t) {
                $list[] = [
                    'id' => $t['item_id'],
                    'name' => $t['item_name']
                ];
            }

            $data['group'][] = [
                'group_id' => $group['group_id'],
                'group_name' => $group['group_name'],
                'list' => $list
            ];
        }

        if (!$data['group']) {
            return redirect('error/doc?msg=没有文档');
        }

        return view('document/wiki/operateManual',$data);
    }

    public function detail(Request $request) {
        $item_id = $request->input('id');

        if ( !$item_id || !ebsig_is_int($item_id) ) {
            return response()->json(['code'=>10000,'message'=>'参数错误']);
        }

        $chart = DocOperateManualGroupItemClass::fetch($item_id);
        if (!$chart) {
            return response()->json(['code'=>10001,'message'=>'文档不存在']);
        }

        return response()->json([
            'code' => 200,
            'message' => 'OK',
            'data' => $chart['doc_html']
        ]);
    }

}