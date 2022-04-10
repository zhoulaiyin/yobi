<?php

namespace App\Http\Controllers\Document;

use App\Models\Classes\Console\Document\DocGroupClass;
use App\Models\Classes\Console\Document\DocGroupItemClass;
use Illuminate\Http\Request;
use App\Http\Models\Document\Docgroupitem;
use App\Http\Models\Document\Docgroup;
use App\Http\Controllers\Controller;
class WikiController extends Controller
{

    private $group_id = 0;

    public function index()
    {
        $limit = [
            'orderBy' => 'sort_order',
            'sort' => 'ASC',
            'pageSize' => '99'
        ];
        $column = 'group_id, group_name';
        $group = DocGroupClass::getList([], $limit, $column);

        $group_id = !$group['count'] ? 0 : $group['data'][0]['group_id'];

        $where = ['group_id' => $group_id];
        $limit = [
            'orderBy' => 'sort_order',
            'sort' => 'ASC',
            'pageSize' => '99'
        ];
        $column = 'item_id, item_name, item_link';
        $item = DocGroupItemClass::getList($where, $limit, $column);

        $data = [
            'group' => $group['data'],
            'group_id' => $group_id,
            'item' => $item['data'],
            'item_id' => !$item['count'] ? 0 : $item['data'][0]['item_id']
        ];

        return view('document/wiki/wiki',$data);
    }

    public function select($group_id) {
        $groupResult = DocGroupClass::fetch($group_id);
        if(empty($groupResult)){
            return response()->json(['code'=>10001,'message'=>'分组不存在']);
        }

        $limit = [
            'orderBy' => 'sort_order',
            'sort' => 'ASC',
            'pageSize' => '99'
        ];
        $column = 'group_id, group_name';
        $group = DocGroupClass::getList([], $limit, $column);

        $where = ['group_id' => $group_id];
        $limit = [
            'orderBy' => 'sort_order',
            'sort' => 'ASC',
            'pageSize' => '99'
        ];
        $column = 'item_id, item_name, item_link';
        $item = DocGroupItemClass::getList($where, $limit, $column)->get()->toArray();

        $data = [
            'group' => $group['data'],
            'group_id' => $group_id,
            'item' => $item['data'],
            'item_id' => empty($item['data']) ? 0 : $item['data'][0]['item_id']
        ];

        return view('document/wiki/wiki',$data);
    }

    public function blank(){
        return view('blank');
    }

}