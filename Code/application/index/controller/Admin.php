<?php
/**
 * Created by PhpStorm.
 * User: chuling
 * Date: 2020/3/20
 * Time: 22:54
 */

namespace app\index\controller;


use app\common\model\Manger;
use think\Controller;
use think\Request;
use think\Session;

class Admin extends Controller
{
    //管理员登录
    public function adminLogin(Request $request)
    {
        $manger = new Manger();
        $post = $request->post();
        $req = Request::instance();
        /**当登录成功
         * 将用户的nickname存进session
         * 为校验数据进行
         */
        $result = $manger->where('nick_Name', $post['nickName'])->select();
        //存在数据
        if ($result) {
            if ($result['password'] == $post['password']) {
                Session::set("nickName", $result['nick_name']);
                Session::set("id", $result['id']);
                session('loginTime', time());
                //登录成功
                return json('success');
            }
        }
        return json("账号密码错误");
    }
}