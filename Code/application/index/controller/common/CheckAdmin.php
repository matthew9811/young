<?php


namespace app\index\controller\common;


use think\Cookie;
use think\Session;

class CheckAdmin extends Base
{

    /**
     * 可以定义控制器初始化方法_initialize，在该控制器的方法调用之前首先执行
     * 这样就不需要在其他controller在书写执行代码了
     * Cookie::set("adminName", $result[0]['nick_name']);
     * Cookie::set("adminId", $result[0]['nick_name'].":id");
     * Cookie::set("adminTime", $result[0]['nick_name'].'loginTime');
     *
     *
     */
    public function _initialize()
    {

        $nowTime = time();
        $nickname = Cookie::get("nickname");
        $loginTime = Cookie::get("loginTime");
        $s_time = Session::get($loginTime);
        parent::_initialize();
        if (!Session::has($nickname)) {
            return $this->error('您没有登陆', url('/index/Index/toLogin'));
        }

        /**
         * 判断登录时间
         */
        if (($nowTime - $s_time) > 36000) {
            /*
             * session('loginTime', null);
             * 清空session中当前用户的信息
             * 实现登出
             */
            Session::delete(Cookie::get("nickname"));
            Session::delete(Cookie::get("id"));
            Session::delete(Cookie::get("loginTime"));
            Cookie::delete("nickname");
            Cookie::delete("id");
            Cookie::delete("loginTime");
            Cookie::clear();
            $this->error('当前用户未登录或登录超时，请重新登录', url('/index/Index/toLogin'));
        } else {
            /**
             * 更新检测时间
             * 这样可以避免对应的数据进行
             */
            Session::set($loginTime, $nowTime);
        }
    }
}