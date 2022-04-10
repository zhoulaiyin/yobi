<?php
namespace App\Http\Controllers\WeBI\wap;

use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Models\WeBI\BiGroup;
use App\Http\Models\WeBI\BiMaster;

use Illuminate\Support\Facades\Redis as Redis;

class GroupListController extends Controller
{

    public function group_list( Request $request) {
        global $WS;

        $data = [];
        $userId = $WS->shopCustID;
        $project_id = $WS->projectID;
        $data['userName'] = $userId;

        //页面数据
        $group_data = BiGroup::select('group_id','group_name')
            ->where([
                ['project_id',$project_id],
                ['bi_user_id',$WS->mainUserID]
            ])
            ->orderBy('group_id','ASC')
            ->get()->toArray();
        if($group_data){

            $data['group'] =$group_data;
        }

        return view('webi/wap/groupList',$data);
    }

    public function bi_list(Request $request)
    {
        global $WS;

        $prj_data = [];
        $project_id = $WS->projectID;
        $data['userName'] = $WS->shopCustID;

        if($request->input('group_id') && $project_id) {
            $prj_data = BiMaster::select('bi_id','bi_title','uid')
                ->where([
                    ['project_id', $project_id],
                    ['group_id', $request->input('group_id')],
                    ['bi_user_id', $WS->mainUserID]
                ])
                ->get()->toArray();
        }
        $data['master'] = $prj_data;
        $data['project_id'] = $project_id;

        return view('webi/wap/biList',$data);
    }

    public function show(Request $request){

        $uid = $request->input('uid');
        if( empty($uid) ){
            return redirect('error/error?msg=您的报表已丢失');
        }

        $bi_master = BiMaster::where('uid',$uid)->first();
        if( empty($bi_master) ){
            return redirect('error/error?msg=您的报表已丢失');
        }

        $dt = [
            'bi_master' => $bi_master->toArray(),
            'uid' => $uid
        ];

        return view('webi/wap/wapShop' , $dt);

    }

}