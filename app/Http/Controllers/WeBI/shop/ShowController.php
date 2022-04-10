<?php

namespace App\Http\Controllers\WeBI\shop;

use App\Models\Classes\Console\BiRuleClass;
use App\Models\Classes\Control\DataSource\DataSourceClass;
use App\Models\Classes\Control\DataSource\DataSourceGroupClass;
use App\Models\Classes\Control\ProjectClass;
use App\Models\Classes\Control\Statement\BiMasterModuleClass;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Common\EbsigHttp;
use App\Http\Controllers\WeBI\WeBIGlobalController;
use App\Models\Classes\Control\Statement\BiMasterClass;
use App\Models\Classes\Console\BiTemplateDatabaseModuleClass;
use App\Models\Classes\Control\DataSource\BiViewClass;
use App\Models\Classes\Control\DataSource\DataTableClass;
use App\Models\Classes\Control\BiUserClass;

class ShowController extends Controller
{
    //1:=, 2:>, 3:>=, 4:<, 5:<=, 6:IN, 7:!=, 8:LIKE, 9:BETWEEN
    public $where_condition = [
        '1' => '=',
        '2' => '>',
        '3' => '>=',
        '4' => '<',
        '5' => '<=',
        '6' => 'IN',
        '7' => '!=',
        '8' => 'LIKE',
        '9' => 'BETWEEN'
    ];

    public $where_condition_mongodb = [
        '1' => '$eq',
        '2' => '$gt',
        '3' => '$gte',
        '4' => '$lt',
        '5' => '$lte',
        '6' => '$in',
        '7' => '$ne',
        '8' => '/',
        '9' => 'BETWEEN'
    ];

    //字符串类型
    public $str_arr = [
        'string','char','varchar','date','datetime'
    ];

    private $bi_view = null;
    private $match = [];//mongodb条件语句
    public $group_id = 0;//视图分组
    public $select_sql = '';
    public $where_sql = '';
    public $where_mongodb = [];
    public $extend_where_sql = ''; //组装门店查询SQL
    public $linkage_where_sql = ''; //组装关联查询SQL
    public $group_by = '';
    public $sort_by = '';
    public $limit = '';
    public $select_fields = []; //查询的数据库字段
    public $fieldsConfig = []; //查询的数据库字段
    public $sort = '';//行列汇总排序

    public function index(Request $request){
        $uid = $request->input('uid');
        if(empty($uid)){
            return redirect('error/error?msg=您的报表已丢失');
        }

        $bi_master = BiMasterClass::get(['uid' => $uid]);
        if(empty($bi_master)){
            return redirect('error/error?msg=您的报表已丢失');
        }

        $dt = [
            'bi_master' => $bi_master,
            'uid' => $uid
        ];

        return view('webi/shop/webiShop' , $dt);
    }

    /**
     * 生成SQL语句，请求接口获取数据
     */
    public function sql(Request $request){
        $args = $request->all();
        $uid = $request->input('uid');
        $param = $request->input('param');

        switch ($args['bi_mode']){
            case 1:
                $bi_module = $bi_master = BiMasterModuleClass::get(['uid' => $uid]);
                break;
            case 2:
                $bi_module = BiTemplateDatabaseModuleClass::fetch($uid);
                break;
        }

        if(empty($bi_module)){
            return response()->json(array('code' => 100000,'message' => '未找到数据','dt'=>[]));
        }

        if(empty($bi_module['db_json'])){
            return response()->json(array('code' => 100001,'message' => '未找到数据','dt'=>[]));
        }

        $db_json = json_decode($bi_module['db_json'],true);
        $bi_json = json_decode($bi_module['bi_json'],true);
        $view_id = !empty($bi_module['view_id']) ? $bi_module['view_id'] : $db_json['view_id'];

        //查询视图
        $this->bi_view = BiViewClass::fetch($view_id);
        if(empty($this->bi_view)){
            return response()->json(array('code' => 100001,'message' => '没有找到数据集信息','dt'=>[]));
        }

        if ($this->bi_view['group_id'] != 0) {
            $this->group_id = DataSourceGroupClass::fetch($this->bi_view['group_id'])['group_classify'];
        }

        if ($this->group_id == 0 || $this->group_id == 2) {
            $this->group_by = $this->sort_by = $this->sort = $this->select_sql = $this->select_fields = [];
        }

        //组合筛选条件
        $this->create_select($db_json);
        if(empty($this->select_sql)){
            return response()->json(array('code' => 100002,'message' => '未找到数据','dt'=>[]));
        }

        //处理扩展查询条件
        $this->create_extend_where($this->bi_view);

        //处理联动数据where条件
        $this->create_linkage_where($param);

        //处理where条件
        $this->create_where($db_json['where'] );

        //处理排序条件
        $this->create_sort($db_json['sort'] );

        //最后的处理
        $this->last_method($db_json);

        //获取视图表
        $WeBIGlobalController = new WeBIGlobalController();
        $table_data = $WeBIGlobalController->get_view_sql($view_id);

        if ($this->group_id == 0 || $this->group_id == 2) { //excel、本地数据源
            $result_data = $this->aggregateSearch($table_data['data']);
        } else {  //sql、微电汇数据源
            $result_data = $this->sqlSearch($table_data['data']);
        }

        if (empty($result_data)) {
            return response()->json(array('code' => 100009,'message' => '未找到数据','dt'=>[]));
        }

        // excel、本地数据源 字段名处理成别名
        //    由于mongodb查询别名无法设置成中文
        if ($this->group_id == 0 || $this->group_id == 2) {
            if(!empty($result_data['data'])){
                $result_list = [];
                foreach ( $result_data['data'] as $d){
                    $temp = [];
                    foreach ($d as $key=>$val){
                        $temp[$this->fieldsConfig[$key]] = $val;
                    }

                    $result_list[] = $temp;
                }

                $result_data['data'] = $result_list;
            }
        }

        //非普通表格
        if( $bi_json['type'] == 'bi_table' && $bi_json['chart_json']['type'] != 'simple' ){
            $data_list = [];
            if(!empty($result_data['data'])){
                foreach ( $result_data['data'] as $d ){
                    $temp = [];
                    $k = 0;
                    foreach ($d as $key=>$val){
                        $temp[$this->select_fields[$k]['alias']] = $val;
                        ++$k;
                    }
                    $data_list[] = $temp;
                }
            }

            return response()->json([
                'code' => 0,
                'count' => $result_data['count'],
                'data' => $data_list
            ]);
        }

        $view_data = WeBIGlobalController::get_view_table($view_id);
        if ($view_data['code'] != 200) {
            return response()->json([ 'code' => $view_data['code'], 'message' => $view_data['message'],]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'uid' => $uid,
            'dt' => $result_data['data'],
            'count' => $result_data['count']
        ]);
    }

    //高级聚合查询
    public function aggregateSearch($table){
        $aggregate = [];

        if (!empty($this->match)) {
            $aggregate[]['$match'] = $this->match;
        }

        $project = [];
        $project['_id'] = 0;
        $project_copy = [];

        if (!empty($this->where_mongodb)) {
            $aggregate[]['$match'] = $this->where_mongodb;
        }

        //查询字段
        if (!empty($this->select_fields)) {
            foreach ($this->select_fields as $i => $dt) {
                $project_copy[$dt['alias']] = '$'.$dt['alias'];
                $project[$dt['alias']] = 1;
            }

            $this->group_by['_id'] = $project_copy;
        }

        //分组统计字段
        if (!empty($this->group_by)) {
            $aggregate[]['$group'] = $this->group_by;
        }

        //排序
        if (!empty($this->sort_by)) {
            foreach ($this->sort_by as $key => $sort) {
                $aggregate[]['$sort'][$key] = strtolower($sort) == 'ASC' ? 1 : -1;
            }
        }

        //下面的分组是为了统计总条数以及将上一步结果放置到list
        $aggregate[]['$group'] = [
            '_id' => null,
            'list' => [
                '$push' => '$$ROOT'
            ],
            'total' => [
                '$sum' => 1
            ]
        ];

        //以下为分页
        if ($this->limit['skip']) {
            $aggregate[]['$skip'] = (int)$this->limit['skip'];
        }

        if ($this->limit['limit']) {
            $aggregate[]['$limit'] = (int)$this->limit['limit'];
        }

        $res = DB::collection($table)->raw(function ($collection) use ($aggregate) {
            return $collection->aggregate($aggregate)->toArray();
        });

        /**
         * 处理返回数据
         * [
         *  [
         *      _id: null,
         *      list: [
         *          [ _id: [所有查询字段] ]
        *       ],
         *      total: number
         *  ]
         * ]
         */

        $list = [];
        $total = 0;
        if (!empty($res)) {
            $total = $res[0]['total'];

            foreach ($res[0]['list'] as $i) {
                $data = [];
                if (!empty($i['_id'])) {
                    foreach ($i['_id'] as $alias => $dt) {
                        $data[$alias] = $dt;
                    }

                    $list[] = $data;
                }
            }
        }

        return ['data' => $list, 'count'=> $total];
    }

    //sql查询
    public function sqlSearch($table){
        //拼装SQL语句
        $search_sql = ' SELECT p_select FROM p_table p_where p_group p_sort p_limit';
        $search_sql = str_replace('p_select', $this->select_sql, $search_sql);
        $search_sql = str_replace('p_table', $table, $search_sql);
        $search_sql = str_replace('p_where', $this->where_sql, $search_sql);
        $search_sql = str_replace('p_group', $this->group_by, $search_sql);
        $search_sql = str_replace('p_sort', $this->sort_by, $search_sql);
        $search_sql = str_replace('p_limit', $this->limit, $search_sql);

        //统计总数SQL
        if(empty($this->group_by)){
            $count_sql = ' SELECT count(*) AS total FROM p_table p_where';
        } else {
            $count_sql = ' SELECT count(*) AS total FROM ( SELECT p_select FROM p_table p_where p_group )dt';
            $count_sql = str_replace('p_select',$this->select_sql,$count_sql);
            $count_sql = str_replace('p_group',$this->group_by,$count_sql);
        }
        $count_sql = str_replace('p_table',$table, $count_sql);
        $count_sql = str_replace('p_where',$this->where_sql, $count_sql);

//        error_log('$this->select_fields-->'.print_r($this->select_fields,true));
//        error_log('$search_sql-->'.$search_sql);
//        error_log('$count_sql-->'.$count_sql);

        $result_data = $this->get_data($search_sql, $count_sql);//查询数据
        if(empty($result_data['data']) ){
            return null;
        }

        return $result_data;
    }

    /**
     * 组合select条件
     */
    public function create_select($db_json){
        $row_fileds = empty($db_json['row']) ? [] : explode(',',$db_json['row']);
        $col_fileds = empty($db_json['column']) ? [] : explode(',',$db_json['column']);
        $sum_fileds = empty($db_json['sum']) ? [] : explode(',',$db_json['sum']);

        if (!empty($row_fileds)){
            foreach ($row_fileds as $row){
                $sub = explode(':',$row);

                $this->selectAndSort($sub);
            }
        }

        if (!empty($col_fileds)) {
            foreach ( $col_fileds as $col ){
                $sub = explode(':',$col);

                $this->selectAndSort($sub);
            }
        }

        if (!empty($sum_fileds)) {
            foreach ($sum_fileds as $sum){
                $sub = explode(':',$sum);
                $alias = substr($sub[0],(stripos($sub[0],'.')+1));

                $this->fieldsConfig[$alias] = isset($sub[1]) ? $sub[1] : $sub[0];
                $this->select_fields[] = [
                    'alias'=> $alias,
                    'name' => isset($sub[1]) ? $sub[1] : $sub[0]
                ];

                switch ($this->group_id) {
                    case 0:
                    case 2:
                            $this->group_by[$alias] = ['$sum' => $alias];

                            if(isset($sub[2]) && $sub[2] != 'undefined'){
                                $this->sort[$alias] = $sub[2];
                            }
                    break;

                    default:

                        if( isset($sub[1]) ){
                            $this->select_sql .= ',SUM('.$sub[0].') AS '.$sub[1];
                        } else {
                            $this->select_sql .= ',SUM('.$sub[0].') AS '.$sub[0];
                        }

                        if( isset($sub[2]) && $sub[2] != 'undefined'){
                            $this->sort .= ','.$sub[0].' '.$sub[2];
                        }
                }
            }

            if(!empty($row_fileds)){
                $groupBy = [];
                foreach ($row_fileds as $f){
                    $groupBy[explode(':',$f)[0]] = '$'.explode(':',$f)[0];
                }

                $this->group_by['_id'] = $groupBy;
            }
        }
    }

    function selectAndSort($sub){
        $alias = substr($sub[0],(stripos($sub[0],'.')+1));
        $this->fieldsConfig[$alias] = isset($sub[1]) ? $sub[1] : $sub[0];
        $this->select_fields[] = [
            'alias'=> $alias,
            'name' => isset($sub[1]) ? $sub[1] : $sub[0]
        ];

        switch ($this->group_id) {
            case 0:
            case 2:
                if (isset($sub[1])) {
                    $this->select_sql[$alias] = $sub[1];
                } else {
                    $this->select_sql[$alias] = $sub[0];
                }

                if (isset($sub[2]) && $sub[2] != 'undefined') {
                    $this->sort[$alias];
                }
                break;

            default:
                if(isset($sub[1])){
                    $this->select_sql .= ','.$sub[0].' AS '.$sub[1];
                } else {
                    $this->select_sql .= ','.$sub[0].' AS '.$sub[0];
                }

                if(isset($sub[2]) && $sub[2] != 'undefined'){
                    $this->sort .= ','.$sub[0].' '.$sub[2];
                }
        }
    }

    /**
     * 组合where条件
     * 并判断当前登录用户角色是否 为 门店 3 或 分部 2 以拼接 指定门店 数据条件
     * @param $where_condition
     * @param $data
     */
    public function create_where($where_condition){
        if(empty($where_condition)){
            return false;
        }

        if ($this->group_id == 0 || $this->group_id == 2) { //mongodb条件拼装
            $this->creat_mongodb_where($where_condition);
        } else {  //sql条件拼装
            $this->creat_sql_where($where_condition);
        }
    }

    public function creat_mongodb_where($where_condition){
        //1:=, 2:>, 3:>=, 4:<, 5:<=, 6:IN, 7:!=, 8:LIKE, 9:BETWEEN
        $mongoWhere = [];

        foreach ($where_condition as $wg) {
            $where = [];
            $child_where = explode(':', $wg);
            $alias = substr($child_where[0],(stripos($child_where[0],'.')+1));

            //例子：'sale_money:销售额:Y:decimal:>:100:2:0'
            switch ( $child_where[6] ) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 7:
                    if (in_array($child_where[3],$this->str_arr)) {
                        $val_str = '"'.$child_where[5]. '"';
                    } elseif($child_where[3] == 'int'){
                        $val_str = (int)$child_where[5];
                    }else {
                        $val_str = $child_where[5];
                    }

                    $where[$alias] = [
                        $this->where_condition_mongodb[$child_where[6]] => $val_str
                    ];
                    break;

                case 6:
                    $val_str = explode(',',$child_where[5]);
                    if ($child_where[3] == 'int') {
                        foreach ($val_str as $i => $val){
                            $val_str[$i]= (int)$val;
                        }
                    }

                    $where[$alias] = [
                        $this->where_condition_mongodb[$child_where[6]] => $val_str
                    ];
                    break;

                case 8:
                    $where[$alias] = [
                        $this->where_condition_mongodb[$child_where[6]] => "/".$child_where[5]."."
                    ];
                    break;

                case 9:
                    $val_arr = explode(',',$child_where[5]);

                    $where[$alias] = [
                        '$gte' => $val_arr[0],
                        '$lte' => $val_arr[1],
                    ];
                    break;
            }

            if ($child_where[2] == 'N'){
                $where = ['$not' => [
                    $where
                ]];
            }

            if (!empty($mongoWhere)) {
                $arr = [
                    $mongoWhere,
                    $where
                ];
            } else {
                $arr = [
                    $where
                ];
            }

            if ($child_where[7] == 0) { //或者
                $conditionWhere['$or'] = $arr;
            } else { //并且
                $conditionWhere['$and'] = $arr;
            }

            $mongoWhere = $conditionWhere;
        }

        $this->where_mongodb = $mongoWhere;
    }

    public function creat_sql_where($where_condition){
        //1:=, 2:>, 3:>=, 4:<, 5:<=, 6:IN, 7:!=, 8:LIKE, 9:BETWEEN
        $condition_string = '';

        //连接or条件
        $or_where_string = '';

        //连接and条件
        $and_where_string = '';

        foreach ($where_condition as $wg){
            $child_where = explode(':',$wg);

            //例子：'sale_money:销售额:Y:decimal:>:100:2:0'
            switch ( $child_where[6] ){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 7:

                    if (in_array($child_where[3],$this->str_arr)) {
                        $val_str = '"'.$child_where[5]. '"';
                    } else {
                        $val_str = $child_where[5];
                    }

                    $condition_string = implode(' ',[
                        $child_where[0],
                        $this->where_condition[$child_where[6]],
                        $val_str
                    ]);
                    break;

                case 6:

                    $val_str = '';

                    if (in_array($child_where[3],$this->str_arr)) {
                        $val_arr = explode(',',$child_where[5]);
                        foreach ( $val_arr as $val ){
                            $val_str .= ',"'.$val. '"';
                        }
                        $val_str = substr($val_str,1);
                    } else {
                        $val_str = $child_where[5];
                    }

                    $condition_string = implode(' ',[
                        $child_where[0],
                        $this->where_condition[$child_where[6]],
                        '('.$val_str.')'
                    ]);
                    break;

                case 8:
                    $condition_string = implode(' ',[
                        $child_where[0],
                        $this->where_condition[$child_where[6]],
                        '%'.$child_where[5].'%'
                    ]);
                    break;

                case 9:

                    $val_arr = explode(',',$child_where[5]);
                    if (in_array($child_where[3],$this->str_arr)) {
                        $val_str = '"'.$val_arr[0].'" AND "'. $val_arr[1]. '"';
                    } else {
                        $val_str = $val_arr[0].' AND '. $val_arr[1]. '';
                    }

                    $condition_string = implode(' ',[
                        $child_where[0],
                        $this->where_condition[$child_where[6]],
                        $val_str
                    ]);
                    break;

            }

            if ($child_where[2] == 'N'){
                $condition_string = ' NOT('.$condition_string.')';
            }

            if ($child_where[7] == 0) {
                $or_where_string .= ' OR  ' . $condition_string;
            } else {
                $and_where_string .= ' AND ' . $condition_string;
            }

        }

        $sql_string = '';
        if( $and_where_string != '' ){
            $sql_string = substr($and_where_string,4);
        }

        if( $or_where_string != '' ){
            if( $sql_string == '' ){
                $sql_string = substr($or_where_string,4);
            } else {
                $sql_string .= $or_where_string;
            }
        }

        $this->where_sql = ' WHERE ' . $sql_string;
    }

    /**
     * 组合order条件
     */
    public function create_sort( $sort_condition ){
        if(empty($sort_condition)){
            return false;
        }

        $sort_group = explode(',',$sort_condition);

        $sortBy = [];
        foreach ($sort_group as $sg){
            $child_sort = explode(':',$sg);
            $sortBy[$child_sort[0]] = $child_sort[2];
        }

        $this->sort_by = $sortBy;
    }

    /**
     * 处理大区和门店角色查询SQL
     */
    public function create_extend_where(){
        global $WS;

        //如果角色为总店账户,则无需处理
        if($WS->userRole == 1){
            return false;
        }

        $tables = json_decode($this->bi_view['table_json'], true);

        //通过默认数据集获取
        function getFromBiRule($tables){
            foreach ($tables as $tb){
                $BiRule = BiRuleClass::get(['table_name' => $tb['table']]);
                if(empty($BiRule)){
                    continue;
                }

                $fields = json_decode($BiRule['fields_json'], true);
                foreach ($fields as $field){
                    if($field['field_remark'] == '门店号'){
                        return $tb['table'] . '.' . $field['field_name'];
                    }
                }
            }

            return '';
        }

        //通过data_source获取
        function getFromDataSource($tables){
            foreach ($tables as $tb){
                $DataTable = DataTableClass::get(['table_name' => $tb['table']]);
                if(empty($DataTable)){
                    continue;
                }

                $fields = json_decode($DataTable['fields_json'], true);
                foreach ($fields as $field){
                    if($field['field_remark'] == '门店号'){
                        return $tb['table'] . '.' . $field['field_name'];
                    }
                }
            }

            return '';
        }

        if($this->bi_view['group_id'] == 0){
            $field = getFromBiRule($tables);
        } else {
            $field = getFromDataSource($tables);
        }

        if(empty($field)){
            return false;
        }

        //查询用户
        $user_data = BiUserClass::fecth($WS->shopUserID);
        if(empty($user_data)){
            return false;
        }

        //组装查询门店SQL，门店号是字符串，两边需要添加引号
        $mall_search_str = '';
        $mall_code_arr = explode(',', $user_data->role_bind);
        foreach ($mall_code_arr as $mall_code){
            if(empty($mall_code)){
                continue;
            }
            $mall_search_str .= ',"' . $mall_code . '"';
        }

        if(empty($mall_search_str)){
            return false;
        }

        $mall_search_str = substr($mall_search_str ,1);

        if($user_data->role_id == 2){
            $this->extend_where_sql = $field . ' IN (' . $mall_search_str . ')';
        } else {
            $this->extend_where_sql = $field . ' = ' . $mall_search_str;
        }
    }

    /**
     * 处理联动数据查询SQL
     */
    public function create_linkage_where($param){
        $data = json_decode($param);
        //联动数据为空，跳过组装
        if (empty($data)) {
            return false;
        }

        $linkage_where = '';

        foreach ($data as $k => $v) {
            $linkage_where .= $k.'="'. $v.'"';
        }

        $this->linkage_where_sql = $linkage_where;
    }

    /**
     * 最终的处理
     */
    public function last_method($db_json){
        //拼接拓展SQL
        if(!empty($this->extend_where_sql)){
            if(empty($this->where_sql)){
                $this->where_sql .= ' WHERE ' . $this->extend_where_sql;
            } else {
                $this->where_sql .= ' AND ' . $this->extend_where_sql;
            }
        }

        //联动数据限制查询
        if (!empty($this->linkage_where_sql)) {
            if(empty($this->where_sql)){
                $this->where_sql .= ' WHERE ' . $this->linkage_where_sql;
            } else {
                $this->where_sql .= ' AND ' . $this->linkage_where_sql;
            }
        }

        switch ($this->group_id) {
            case 0:
            case 2:
                $this->sort_by = array_merge($this->sort, $this->sort_by);
                $this->limit = [
                    'limit' => 0,
                    'skip' => 0
                ];

                //存在数量限制
                if(!empty($db_json['limit'])){
                    $this->limit['limit'] = $db_json['limit'];
                }

                //存在传入的分页参数，此时模板为表格类型
                if( !empty($args['page']) && !empty($args['limit']) ){
                        $this->limit['skip'] = ($args['page']-1) * $args['limit'];
                }
                break;

            default:
                if(strlen($this->sort) > 1){
                    if( $this->sort_by != "" ){
                        $this->sort_by = $this->sort_by .''. substr($this->sort,1);
                    }else{
                        $this->sort_by = ' ORDER BY ' . substr($this->sort,1);
                    }
                }

                //存在数量限制
                if( !empty($db_json['limit']) ){
                    $this->limit = ' LIMIT ' . $db_json['limit'];
                }


                //存在传入的分页参数，此时模板为表格类型
                if( !empty($args['page']) && !empty($args['limit']) ){
                    $this->limit = ' LIMIT ' . ( ($args['page']-1)*$args['limit'] ) . ',' . $args['limit'];
                }
        }
    }

    /**
     * 根据sql查询数据
     */
    public function get_data($search_sql, $count_sql = null){
        global $WS;
        $result = [
            'count' => 0,
            'data' => []
        ];

        switch ($this->group_id){
            case 1:
                    //查询项目信息
                    $project_master = ProjectClass::fetch($WS->projectID);
                    if( !empty($project_master) ){
                        $api_url = $project_master['project_domain_name'].'/external/webi.php';
                        $api_result = EbsigHttp::post($api_url,['sql'=>base64_encode($search_sql),'count'=>base64_encode($count_sql)]);
                        if( $api_result['code'] == 200 && !empty($api_result['data']['data']) ){
                            $result['data'] = $api_result['data']['data'];
                            $result['count'] = $api_result['data']['count'];
                        }
                    }

                break;

            case 0:
            case 2:
//                    $result['data'] = DB::query($search_sql);
//                    $count_result = DB::query($count_sql);
//                    error_log('$count_result---'.print_r($count_result, true));
//                    $result['count'] = empty($count_result) ? 0 : $count_result[0]['total'];
                break;

            case 3:
                $data_list = DataSourceClass::conn_query($this->bi_view['source_id'],$search_sql);
                $count_result = DataSourceClass::conn_query($this->bi_view['source_id'],$count_sql);
                $result['data'] = $data_list['data'];
                $result['count'] = empty($count_result) ? 0 : $count_result['data'][0]['total'];
                break;

            default:
                error_log('无效的数据源类型');
        }

        return $result;
    }

}