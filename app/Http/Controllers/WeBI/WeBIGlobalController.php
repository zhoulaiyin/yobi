<?php
namespace App\Http\Controllers\WeBI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\WeBI\BiView;

use App\Models\Classes\Control\Statement\BiMasterClass;
use App\Models\Classes\Control\Statement\BiMasterModuleClass;
use App\Models\Classes\Control\DataSource\BiViewClass;
use App\Models\Classes\Control\DataSource\DataTableClass;
use App\Models\Classes\Console\BiRuleClass;

class WeBIGlobalController extends Controller
{
    private $global_op = []; //已经遍历的元素UID集合
    private $lowest_uid = ''; //当前遍历到的最深处的UID
    private $create_join_sql = ''; //整体的链表SQL
    private $table_json = []; //解析的table链表数据
    private $join_type = [
        '1' => ' LEFT JOIN ',
        '2' => ' INNER JOIN ',
        '3' => ' RIGHT JOIN '
    ];

    public static function get($unique_id, $flg){
        if ($flg == 1) { //按主键ID查询
            $bi_master = BiMasterClass::fetch($unique_id);
        } else { //按UUID查询
            $bi_master = BiMasterClass::get(['uid' => $unique_id]);
        }

        if( empty($bi_master) ){
            return ['code'=>404,'message'=>'没有找到数据'];
        }

        //调用报表接口数组
        $webi_dt = array(
            'master' => json_decode($bi_master['attribute_json'], true), //全局属性
            'module' => array(),
            'bi_id' => $bi_master['_id']
        );

        //操作对象
        $moduleResult = BiMasterModuleClass::getList(['bi_id' => $bi_master['_id']], ['pageSize' => 999]);
        if ($moduleResult['count'] > 0) {
            foreach ($moduleResult['data'] as $k=>$v) {

                //子模板
                if ( !isset($webi_dt['module'][$v['uid']]) ) {
                    $webi_dt['module'][$v['uid']] = [
                        'bi_json' => json_decode($v['bi_json'], true),
                        'db_json' => json_decode($v['db_json'], true),
                        'attribute_json' => json_decode($v['attribute_json'], true),
                        'chart_json' => json_decode($v['chart_json'], true)
                    ];
                }

            }
        }

        return ['code'=>200,'message'=>'ok','data'=>$webi_dt];
    }

    /**
     * 通过BI的标识（ID或者uid），获取相应的数据结构数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function series(Request $request){
        $uid = $request->input('uid');
        if( empty($uid) ){
            return response()->json(array('code' => 400,'msg' => '缺失参数'));
        }

        $dt = WeBIGlobalController::get($uid,2);
		
        return response()->json( $dt );
    }

    /**
     * 获取视图下面所有数据库表和对应的字段信息
     * @param $view_id 视图ID
     */
    public static function get_view_table($view_id){
        global $WS;

        $redis_dt = $WS->sessionGet('G_BI_VIEW_TABLE_'. $view_id, true);
        if(!empty($redis_dt)){
            return ['code'=>200,'message'=>'redis','data'=> $redis_dt];
        }

        $bi_view = BiViewClass::fetch($view_id);
        if(!$bi_view){
            return ['code'=>10000,'message'=>'没有找到数据集信息'];
        }

        if (empty($bi_view['table_json'])){
            return ['code'=>10001,'message'=>'没有找到数据集信息~'];
        }

        if(empty($bi_view['table_json'])){
            return ['code'=>10002,'message'=>'无法获取数据集信息'];
        }

        $table_json = json_decode($bi_view['table_json'], true);

        //返回的数据
        $rtn_dt = [];
        foreach ($table_json as $uid => $dt){
            if (empty($dt['table'])) {
                continue;
            }

            //已经存在的表名数据，跳过
            if(!empty($rtn_dt[$dt['table']])){
                continue;
            }

            if ($bi_view['group_id'] != 0){
                //查询链表的表名
                $where = [
                    ['source_id',$bi_view['source_id']],
                    ['table_name',$dt['table']],
                ];

                $DataTable = DataTableClass::getList($where, ['pageSize' => 999]);
                if(empty($DataTable['data']) || empty($DataTable['data'][0]) ){
                    continue;
                }

                $rule = $DataTable['data'][0];
            } else {
                //查询链表的表名
                $where = [
                    'table_name' => $dt['table']
                ];

                $rule = BiRuleClass::get($where);
            }

            if(empty($table_json)){
                continue;
            }

            $rtn_dt[$dt['table']] = [
                'desc' => $rule['description'],
                'field' => json_decode($rule['fields_json'], true),
                'table_name' => $dt['table_name']
            ];
        }

        if(empty($rtn_dt)){
            $rtn_dt = '';
        }

        //存储redis，保存时间为7天
        if (!empty($rtn_dt)) {
            $WS->sessionSet('G_BI_VIEW_TABLE_'. $view_id, $rtn_dt, 86400*7, true);
        }

        return ['code'=>200,'message'=>'ok','data'=>$rtn_dt];
    }

    /**
     * 获取视图下的链表SQL
     *  1、寻找最深的子节点
     *  2、查找最深子节点是否存在同级元素，若存在则处理，回归到1过程；否则进入3
     *  3、向上遍历所有元素
     * @param $view_id 视图ID
     */
    public function get_view_sql($view_id){
        global $WS;

        $redis_dt = $WS->sessionGet('G_BI_VIEW_TABLE_SQL_'. $view_id, true);
        if( !empty($redis_dt) ){
            return ['code'=>200,'message'=>'ok','data'=>$redis_dt];
        }

        $bi_view = BiViewClass::fetch($view_id);
        if(!$bi_view){
            return ['code'=>10000,'message'=>'没有找到视图信息','data'=>''];
        }

        if (empty($bi_view['table_json'])) {
            return ['code'=>10001,'message'=>'没有找到视图包含的表信息','data'=>''];
        }

        $table_json = json_decode($bi_view['table_json'], true);

        $this->table_json = $table_json;

        reset($this->table_json);
        $first_elem = current($this->table_json);

        $this->create_join_sql = $first_elem['table'];

        $this->get_heightest_elem($first_elem['uid']);
        $this->backflow($this->lowest_uid);

        //存储redis，保存时间为7天
        $WS->sessionSet('G_BI_VIEW_TABLE_SQL_'. $view_id, $this->create_join_sql, 86400*7, true);

        return ['code'=>200,'message'=>'ok','data'=>$this->create_join_sql];
    }

    /**
     * 根据当前节点，一直遍历到最深子节点
     * @param $uid
     * @return mixed
     */
    public function get_heightest_elem($uid){
        $this->global_op[] = $uid;

        if( empty($this->table_json[$uid]['join']) ){
            $this->lowest_uid = $uid;//遍历到了最后一个节点
            return $uid;
        }

        //获取第一个子元素
        reset($this->table_json[$uid]['join']);
        $temp_dt = current($this->table_json[$uid]['join']);
        $temp_uid = key($this->table_json[$uid]['join']);
        $parent_elem = $this->table_json[$uid];

        //创建链表SQL
        $this->create_join_sql($parent_elem['table'],$temp_dt);

        //递归深入到最小子节点
        $this->get_heightest_elem($temp_uid);
    }

    /**
     * 往回循环遍历
     * @param $uid
     * @return bool
     */
    public function backflow ($uid){
        $parent_uid = $this->table_json[$uid]['parent'];
        if( empty($parent_uid) ){
            return false;
        }

        //遍历所有子元素
        foreach ($this->table_json[$parent_uid]['join'] as $uuid =>$relation){
            if( in_array($uuid,$this->global_op) ){
                continue;
            }

            $this->create_join_sql($this->table_json[$this->table_json[$uid]['parent']]['table'],$relation);
            $this->get_heightest_elem($uuid);
            $this->backflow($this->lowest_uid);
        }

        $this->backflow($this->table_json[$this->table_json[$uid]['parent']]['uid']);
    }

    /**
     * 生成链接表SQL语句
     * @param $parent_table
     * @param $son_dt
     */
    public function create_join_sql($parent_table,$son_dt){
        $relation = json_decode($son_dt[2],true);
        $on_sql = '';

        $temp_create_sql = '';
        $temp_create_sql .= $this->join_type[$son_dt[1]];
        $temp_create_sql .= $son_dt[0];
        $temp_create_sql .= ' ON ';

        foreach ( $relation as $key=>$join ){
            $on_sql .= ',' . $parent_table . '.' . $key . ' = ' . $son_dt[0] . '.' . $join;
        }

        $temp_create_sql .= substr($on_sql,1);

        $this->create_join_sql =   '(' . $this->create_join_sql . $temp_create_sql . ')';
    }

}