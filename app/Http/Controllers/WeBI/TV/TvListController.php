<?php
namespace App\Http\Controllers\WeBI\TV;

use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Models\WeBI\BiGroup;
use App\Http\Models\WeBI\BiMaster;

use Illuminate\Support\Facades\Redis as Redis;

class TvListController extends Controller
{
    public function group_list( Request $request) {
        global $WS;

        $data = [];
        $project_id = $WS->projectID;
        $data['userName'] = $WS->shopCustID;

        //页面数据
        $group_data = BiGroup::select('group_id','group_name')
            ->where([
                ['project_id',$project_id],
                ['bi_user_id',$WS->mainUserID]
            ])
            ->orderBy('group_id','ASC')
            ->get()->toArray();
        if($group_data){
            $data['group_id'] =$group_data[0]['group_id'];
            $data['group'] =$group_data;
        }

        return view('webi/TV/groupList',$data);
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

        return response()->json( array('code' =>200 ,'msg' => 'ok', 'data'=>$data ) );
    }

    public function show_bi($uid){

        $dt = [
            'uid' => $uid
        ];
        return view('webi/TV/webiShow' , $dt);

    }

    public function get_redis(Request $request){

        $uid = $request->input('uid');
        $equipment_code = $request->input('equipment_code');

        if( !empty($request->input('iType'))  ){//手机端点击报表保存redis

            if( empty($uid) || empty($equipment_code) ){
                return response()->json(array('code' => 404,'msg' => '缺失参数'));
            }

            if( $request->input('iType') == 2 ){
                $setKey = '2,'.$uid;
            }else{
                $setKey = '1,'.$uid;
            }
            Redis::setex('BI_APP_SELECTED_' . $equipment_code, 60 , $setKey);

        }else{

            $redis_data = Redis::get('BI_APP_SELECTED_' .$equipment_code);
            if( !empty($redis_data) ){
                Redis::del('BI_APP_SELECTED_' . $equipment_code);
            }
            return response()->json( array('code' =>200 ,'msg' => 'ok', 'data'=>explode(',',$redis_data) ) );
        }

    }
}