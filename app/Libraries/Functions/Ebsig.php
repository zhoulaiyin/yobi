<?php

function isShopLogin() {
    $login_session = Illuminate\Support\Facades\Redis::get('WeBI_SHOP_LOGIN_SESSION' . session()->getId());
    return empty($login_session) ? false : true;
}

function shopUserId() {
    $login_session = Illuminate\Support\Facades\Redis::get('WeBI_SHOP_LOGIN_SESSION' . session()->getId());
    if( empty($login_session) ){
        return null;
    }
    $login_session = json_decode($login_session,true);
    return isset($login_session['BI_USER_ID']) ? $login_session['BI_USER_ID'] : null;
}

function shopUserName() {
    $login_session = Illuminate\Support\Facades\Redis::get('WeBI_SHOP_LOGIN_SESSION' . session()->getId());
    if( empty($login_session) ){
        return null;
    }
    $login_session = json_decode($login_session,true);
    return isset($login_session['USER_NAME']) ? $login_session['USER_NAME'] : null;
}

function shopUserProjectID() {
    $login_session = Illuminate\Support\Facades\Redis::get('WeBI_SHOP_LOGIN_SESSION' . session()->getId());
    if( empty($login_session) ){
        return null;
    }
    $login_session = json_decode($login_session,true);
    return isset($login_session['PROJECT_ID']) ? $login_session['PROJECT_ID'] : null;
}

function isLogin() {
    $login_session = Illuminate\Support\Facades\Redis::get('ADMIN_LOGIN_SESSION' . session()->getId());
    return empty($login_session) ? false : true;
}

function UserId() {
    $login_session = Illuminate\Support\Facades\Redis::get('ADMIN_LOGIN_SESSION' . session()->getId());
    if( empty($login_session) ){
        return null;
    }
    $login_session = json_decode($login_session,true);
    return isset($login_session['USER_ID']) ? $login_session['USER_ID'] : null;
}

function UserName() {
    $login_session = Illuminate\Support\Facades\Redis::get('ADMIN_LOGIN_SESSION' . session()->getId());
    if( empty($login_session) ){
        return null;
    }
    $login_session = json_decode($login_session,true);
    return isset($login_session['USER_NAME']) ? $login_session['USER_NAME'] : null;
}

function has_permission($permission_id) {
    if (!isset($permission_id) || !is_numeric($permission_id) || $permission_id <= 0) {
        return false;
    }
    $login_session = Illuminate\Support\Facades\Redis::get('ADMIN_LOGIN_SESSION' . session()->getId());
    if( empty($login_session) ){
        return false;
    }
    $login_session = json_decode($login_session,true);
    if( empty($login_session['USER_PERMISSIONS']) ){
        return false;
    }
    return array_key_exists ( $permission_id, $login_session ['USER_PERMISSIONS'] );
}

function url_check($aclURI) {

    $session_id = session()->getId();
    $PERMISSIONED_URI = Illuminate\Support\Facades\Redis::get('PS_PERMISSIONEDURI' . $session_id);

    if( empty($PERMISSIONED_URI) ){
        $ttl = Illuminate\Support\Facades\Redis::ttl('ADMIN_LOGIN_SESSION' . $session_id);
        $PERMISSIONED_URI = [];
        $result_puri = DB::table('permission')->select('permission_id','permission_url')->get()->toArray();
        foreach ( $result_puri as $p_uri ) {
            $PERMISSIONED_URI [md5 ( $p_uri->permission_url )] = $p_uri->permission_id;
        }
        Illuminate\Support\Facades\Redis::setex('PS_PERMISSIONEDURI'.$session_id , $ttl , json_encode($PERMISSIONED_URI) );
    } else {
        $PERMISSIONED_URI = json_decode($PERMISSIONED_URI, true);
    }

    $aclURI_key = md5 ( $aclURI );
    if (key_exists ( $aclURI_key, $PERMISSIONED_URI )) {
        return has_permission ( $PERMISSIONED_URI [$aclURI_key] );
    } else {
        return true;
    }
}

/**
 * ??????UUID????????????
 * @return string
 */
function makeUuid()
{
    $address = strtolower('localhost' . '/' . '127.0.0.1');
    list ( $usec, $sec ) = explode(" ", microtime());
    $time = $sec . substr($usec, 2, 3);
    $random = rand(0, 1) ? '-' : '';
    $random = $random . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999) . rand(100, 999) . rand(100, 999);
    $uuid = strtoupper(md5($address . ':' . $time . ':' . $random));
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20);
    $uuid = str_replace("-", "", $uuid);
    return $uuid;
}

/**
 * ?????????????????????????????????
 * ?????????????????????11???????????????????????????????????????1????????????????????????34568??????????????????
 * @param string $val ????????????
 * @return bool
 */
function isMobile($val) {
    return preg_match('/^1\d{10}$/', $val);
}

/**
 * ?????????????????????????????????
 * 3-4????????????7-8??????????????????1???4????????????
 * @param string $val ????????????
 * @return bool
 */
function isPhone($val) {
    return preg_match('/^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?$/', $val);
}

/**
 * ???????????????????????????
 * ???????????????6-30????????????????????????_?????????-?????????
 * @author ?????????
 * @param string $val ??????
 * @return bool
 */
function is_pwd($val) {
    return preg_match('/^[\w-]{6,30}$/', $val);
}

/**
 * ?????????????????????????????????
 * @author ?????????
 * @param string $email email
 * @return bool
 */
function isEmail($email) {
    return preg_match('/^[\w-]+(\.[\w-]+)*\@[A-Za-z0-9]+((\.|-|_)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/', $email);
}

/**
 * ???????????????????????????????????? HTML ??????
 * @param string $str
 * @return string
 */
function convertVar($str) {

    if (!isset($str) || empty($str))
        return null;
    return htmlspecialchars(trim($str));
}
function convert_var($str) {

    if (!isset($str) || empty($str))
        return null;
    return htmlspecialchars(trim($str));

}

/**
 * ?????????????????????
 * @param string $val ???
 * @param int $type ???????????????1.??????0????????? 2.????????????????????? 3.??????0????????? 4.????????????0????????????
 * @return bool
 */
function ebsig_is_int($val, $type = 1) {

    if (ceil($val) != $val)
        return false;

    if ($type == 1 && $val <= 0)
        return false;
    else if ($type == 2 && $val < 0)
        return false;
    else if ($type == 3 && $val >= 0)
        return false;
    else if ($type == 4 && $val > 0)
        return false;

    return true;
}

/**
 * ????????????????????????????????????+??????
 * @param $val
 * @return bool
 */
function is_true_date( $val ) {
    return preg_match('/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/', $val);
}

/**
 * $str?????????????????????????????????$length????????????????????????????????????????????????????????????
 * @param string $str ???????????????????????????
 * @param int $length ????????????
 * @return string
 */
function str_cut($str, $length) {
    $str = trim($str);
    if (mb_strlen($str, 'utf-8') > $length) {
        $string = mb_substr($str, 0, $length, 'utf-8');
        $string .= '...';
        return $string;
    }
    return $str;
}

/**
 * ????????????
 * @param int $pageIndex ????????????
 * @param int $count ?????????
 * @param int $limit ??????????????????
 * @param string $link ??????????????????????????????????????????%d??????????????????????????????sprintf????????????%d????????????
 * @param string $tpl ????????????
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
 */
function page($pageIndex, $count, $limit, $link, $tpl = 'page.page') {

    $pageCount = ceil($count / $limit);
    if ($pageCount == 1)
        return null;

    $pageLinks = [];
    if ($pageIndex > 1) {
        $pageLinks['previous']['link'] = sprintf($link, $pageIndex - 1);
        $pageLinks['previous']['page'] = $pageIndex - 1;
    }
    if ($pageIndex < $pageCount) {
        $pageLinks['next']['link'] = sprintf($link, $pageIndex + 1);
        $pageLinks['next']['page'] = $pageIndex + 1;
    }
    $i = 1;
    while ($i <= $pageCount) {
        $pageLinks['link'][] = array('href' => sprintf($link, $i), 'text'=> $i);
        if ($pageIndex - 3 > $i) {
            $pageLinks['link'][] = array('href' => '', 'text'=> '...');
            $i = $pageIndex - 3;
        } else if ($i < $pageCount && $pageIndex + 3 < $i && $pageCount - 1 > $i) {
            $pageLinks['link'][] = array('href'=>'', 'text'=>'...');
            $i = $pageCount - 1;
        }
        $i++;
    }
    $pageLinks['pageIndex'] = $pageIndex;
    $pageLinks['total'] = $count;
    $pageLinks['pageCount'] = $pageCount;
    $pageLinks['skip_link'] = $link;

    return view($tpl, ['pageLinks'=>$pageLinks]);

}

/**
 * ???????????????
 * @param string $str ????????????????????????
 * @return string
 */
function encryptD($str) {

    $seed = 2;
    $len = strlen($str);
    $result = '';
    for ($i = 0; $i < $len; $i++) {
        $o = ord(substr($str, $i, 1));
        $b = $o ^ $seed << 2;
        $result .= chr($b);
    }
    $result .= chr(($seed + 64) ^ 35);
    return base64_encode($result);

}

/**
 * ???????????????
 * @param string $str ????????????????????????
 * @return string
 */
function decryptD($str) {

    $str_d = base64_decode($str);
    $seed = (ord(substr($str_d, -1)) - 64) ^ 35;
    $len = strlen($str_d) - 1;
    $result = '';
    for ($i = 0; $i < $len; $i++) {
        $o = ord(substr($str_d, $i, 1));
        $b = $o ^ $seed << 2;
        $result .= chr($b);
    }

    return $result;

}


/** Json???????????????
 * @param  Mixed  $data   ??????
 * @param  String $indent ?????????????????????4?????????
 * @return string
 */
function jsonFormat($data, $indent=null){

    // ????????????????????????????????????urlencode???????????????????????????
    array_walk_recursive($data, 'jsonFormatProtect');

    // json encode
    $data = json_encode($data);

    // ???urlencode???????????????urldecode
    $data = urldecode($data);

    // ????????????
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent)? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;

    for($i=0; $i<=$length; $i++){

        $char = substr($data, $i, 1);

        if($char=='"' && $prevchar!='\\'){
            $outofquotes = !$outofquotes;
        }elseif(($char=='}' || $char==']') && $outofquotes){
            $ret .= $newline;
            $pos --;
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }

        $ret .= $char;

        if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
            $ret .= $newline;
            if($char=='{' || $char=='['){
                $pos ++;
            }

            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }

        $prevchar = $char;
    }

    return $ret;
}


/** ?????????????????????urlencode
 * @param String $val
 */
function jsonFormatProtect(&$val){
    if($val!==true && $val!==false && $val!==null){
        $val = urlencode($val);
    }
}

/**
 * ???????????????
 * @param string $name ???????????????
 * @param null|string $prefix ??????
 * @param bool $is_transaction ????????????
 * @return null|string
 */

function generate_seqno($name, $prefix=null, $is_transaction = true) {

    $seq_no = null;
    $seq_no_ge = null;

    if (!$is_transaction) {
        App\Models\Console\System\SysSeqno::where(['name'=>$name])->increment('seqno', 1);
        $seq_no_data = $seqno_data = App\Models\Console\System\SysSeqno::where('name',$name)->first();

        if (!is_null($seq_no_data) ) {
            $seq_no = $seq_no_data['seqno'];
        }
    } else {

        try {
            //????????????
            DB::beginTransaction();

            App\Models\Console\System\SysSeqno::where(['name'=>$name])->increment('seqno', 1);
            $seq_no_data = $seqno_data = App\Models\Console\System\SysSeqno::where('name',$name)->first();

            if ( !is_null($seq_no_data) ) {
                $seq_no = $seq_no_data['seqno'];
            }

            //????????????
            DB::commit();
        } catch (Exception $e) {

            //????????????
            DB::rollBack();

            $seq_no = null;
        }
    }

    if ( is_null($seq_no) ) {
        return null;
    }

    switch ($name) {
        case 'contract_template_code':
            $seq_no_ge = str_repeat('0', 3 - strlen($seq_no)) . $seq_no;
            break;

        case 'role_group_code':
            $seq_no_ge = str_repeat('0', 2 - strlen($seq_no)) . $seq_no;
            break;

        case 'contract_code':
            $seq_no_ge = str_repeat('0', 3 - strlen($seq_no)) . $seq_no;
            break;

        default:
            $seq_no_ge = $seq_no;
            break;
    }

    if (!is_null($prefix))
        $seq_no= $prefix . $seq_no_ge;

    return $seq_no_ge;
}

/**
 * ?????????????????????
 * @param $dir ??????
 * @return int ??????????????????b???
 */
function getDirSize($dir){
    $handle = opendir($dir);
    $sizeResult = 0;
    while (false!==($FolderOrFile = readdir($handle))) {
        if($FolderOrFile != "." && $FolderOrFile != "..") {
            if(is_dir("$dir/$FolderOrFile")) {
                $sizeResult += getDirSize("$dir/$FolderOrFile");
            }else {
                $sizeResult += filesize("$dir/$FolderOrFile");
            }
        }
    }
    closedir($handle);
    return $sizeResult;
}

/**
 * ????????????????????????
 * @param $size ?????????????????????????????????b???
 * @return string
 */
function getRealSize($size){
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if($size < $kb){
        return $size."B";
    }else if($size < $mb) {
        return round($size/$kb,2)."KB";
    } else if($size < $gb) {
        return round($size/$mb,2)."MB";
    }else if($size < $tb){
        return round($size/$gb,2)."GB";
    }else {
        return round($size/$tb,2)."TB";
    }
}