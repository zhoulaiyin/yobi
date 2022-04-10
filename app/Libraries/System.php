<?php

use Illuminate\Support\Facades\Redis as Redis;

class WS {

    public function __construct()
    {
        //
    }

    public function __get($name)
    {

        switch ( $name ){

            case 'shopUserID' : //shop端会员号
                if ($login_session = $this->sessionGet ('SHOP_LOGIN_SESSION')) {
                    if (isset ( $login_session ['BI_USER_ID'] ))
                        return $login_session ['BI_USER_ID'];
                }
                return null;
                break;

            case 'shopCustID' : //前端会员名
                if ($login_session = $this->sessionGet ('SHOP_LOGIN_SESSION')) {
                    if (isset ( $login_session ['USER_NAME'] ))
                        return $login_session ['USER_NAME'];
                }
                return null;
                break;

            case 'mainUserID' : //主操作人ID
                if ($login_session = $this->sessionGet ('SHOP_LOGIN_SESSION')) {
                    if (isset ( $login_session ['MAIN_USER_ID'] ))
                        return $login_session ['MAIN_USER_ID'];
                }
                return null;
                break;

            case 'isCustomerLogin' : //判断前台会员是否登录
                $login_session = $this->sessionGet('SHOP_LOGIN_SESSION');
                return empty($login_session) ? false : true;
                break;

            case 'userRole' : //前台会员角色
                if ($login_session = $this->sessionGet ('SHOP_LOGIN_SESSION')) {
                    if (isset ( $login_session ['USER_ROLE'] ))
                        return $login_session ['USER_ROLE'];
                }
                return null;
                break;

            case 'userPermission' : //前台会员权限
                if ($login_session = $this->sessionGet ('SHOP_LOGIN_SESSION')) {
                    if (isset ( $login_session ['USER_PERMISSION'] ))
                        return $login_session ['USER_PERMISSION'];
                }
                return null;
                break;

            case 'currentUserID' : //后台用户ID
                if ($login_session = $this->sessionGet ('ADMIN_LOGIN_SESSION')) {
                    if (isset ( $login_session ['USER_ID'] ))
                        return $login_session ['USER_ID'];
                }
                return null;
                break;

            case 'currentUserName' : //后台登录用户名
                if ($login_session = $this->sessionGet ('ADMIN_LOGIN_SESSION')) {
                    if (isset ( $login_session ['USER_NAME'] ))
                        return $login_session ['USER_NAME'];
                }
                return null;
                break;

            case 'isUserLogin' : //判断后台用户是否登录
                $login_session = $this->sessionGet('ADMIN_LOGIN_SESSION');
                return empty($login_session) ? false : true;
                break;

            case 'projectID' : //前台项目ID
                if ($login_session = $this->sessionGet ('SHOP_LOGIN_SESSION')) {
                    if (isset ( $login_session ['PROJECT_ID'] ))
                        return $login_session ['PROJECT_ID'];
                }
                return null;
                break;
        }

    }

    public function sessionSet($key, $value, $timeout, $global = false) {
        if ($global)
            $tmp_key = $key;
        else
            $tmp_key = session()->getId() . $key;
        return Redis::setex($tmp_key, $timeout , json_encode($value) );
    }

    public function sessionGet($key, $global = false){
        $redis_val = Redis::get($global===false ? session()->getId() . $key : $key );
        return empty($redis_val) ? null : json_decode($redis_val,true);
    }

    public function sessionRemove($key, $global = false){
        if ($global)
            $tmp_key = $key;
        else
            $tmp_key = session()->getId() . $key;

        return Redis::del($tmp_key);
    }

    /**
     * 判断是否拥有权限
     * @param $permission_id
     * @return bool
     */
    public function hasPermission($permission_id) {
        if (!isset($permission_id) || !is_numeric($permission_id) || $permission_id <= 0) {
            return false;
        }
        $login_session = $this->sessionGet('ADMIN_LOGIN_SESSION');
        if( empty($login_session) ){
            return false;
        }

        if( empty($login_session['USER_PERMISSIONS']) ){
            return false;
        }
        return array_key_exists ( $permission_id, $login_session ['USER_PERMISSIONS'] );
    }

    /**
     * 判断是否拥有某个页面的访问权限
     * @param $aclURI
     * @return bool
     */
    public function urlCheck($aclURI) {

        $PERMISSIONED_URI = $this->sessionGet('ADMINLOGIN_PERMISSIONEDURI');

        if( empty($PERMISSIONED_URI) ){
            $PERMISSIONED_URI = [];
            $result_puri = DB::table('permission')->select('permission_id','permission_url')->get();
            if($result_puri){
                $result_puri = $result_puri->toArray();
                foreach ( $result_puri as $p_uri ) {
                    $PERMISSIONED_URI [md5 ( $p_uri['permission_url'] )] = $p_uri['permission_id'];
                }
                $this->sessionSet('ADMINLOGIN_PERMISSIONEDURI',$PERMISSIONED_URI,86400);
            }
        }

        $aclURI_key = md5 ( $aclURI );
        if (key_exists ( $aclURI_key, $PERMISSIONED_URI )) {
            return $this->hasPermission ( $PERMISSIONED_URI [$aclURI_key] );
        } else {
            return true;
        }
    }

    /**
     * 载入权限
     * @return array
     */
    public function loadPermission($user=null) {

        if( empty($user) ){
            $user = DB::table('user')->where('id', $this->currentUserID)->first();
        }

        $login_session['USER_PERMISSIONS'] = [];
        $login_session['USER_ID'] = $user['user_id'];
        $login_session['USER_NAME'] = $user['true_name'];
        $login_session['USER_MOBILE'] = $user['mobile'];

        $this->sessionSet('ADMIN_LOGIN_SESSION',$login_session,86400);
        $this->sessionRemove('HOMLOGIN_PERMISSIONEDURI');

        return ['code'=>200,'msg'=>'ok'];
    }

    /**
     * 保存cookie数据
     * @param string $name cookie名
     * @param string $value cookie值
     * @param int $timeout cookie时效
     * @return bool
     */
    public function setCookie($name, $value, $timeout=0) {
        if (!isset($name) || empty($name)) {
            return false;
        }
        if (!isset($value) || empty($value)) {
            return false;
        }
        $cookie_domain = str_replace('www', '', $_SERVER['SERVER_NAME']);
        if ($timeout == 0) {
            return setcookie($name, urlencode($value), 0, '/', $cookie_domain);
        } else {
            return setcookie($name, urlencode($value), time() + $timeout, '/', $cookie_domain);
        }
    }

    /**
     * 获取cookie数据
     * @param string $name cookie名
     * @return null|string
     */
    public function getCookie($name) {
        if (!isset($name) || empty($name)) {
            return null;
        }
        if (isset($_COOKIE[$name])) {
            return urldecode($_COOKIE[$name]);
        } else {
            return null;
        }
    }

    /**
     * 判断前台登陆者是否具有设计BI权限
     */
    public function isDesigner(){
        $login_session = $this->sessionGet ('SHOP_LOGIN_SESSION');
        if(empty($login_session)){
            return false;
        }

        return !in_array(2,$login_session['USER_PERMISSION']) ? false : true;
    }

}

$WS = new WS();