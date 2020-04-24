<?php

namespace app\index\controller;

use app\common\model\Manger;
use app\common\model\User;
use app\index\controller\common\Base;
use think\Cookie;
use think\Db;
use think\Request;
use think\Session;

class Index extends Base
{
    public function index()
    {
        //判断用户是否登录
        $userId = Cookie::get("id");
        if ($userId != '') {
            $id = Session::get($userId);
            $user = Base::getUser($id)[0];
            $userArt = Base::getUserArt($id);
            $userCollectArt = Base::getUserCollectArt($id);
            $this->assign('user',$user);
            $this->assign('userArt',$userArt);
            $this->assign('userCollect',$userCollectArt);
        }
        $newArt = model("common/Article")->where("review_status", '1')
            ->order('issuing_time desc')->limit(5)->select();
        for ($i = 0; $i < count($newArt); $i++) {
            $customerId = $newArt[$i]->customer_id;
            $articleId = $newArt[$i]->id;
            $newArt[$i]['customer'] = Db::table('user')
                ->where('id', $customerId)->value('nick_name');
            $collect = Db::table('collect')->where('article_id', $articleId)->select();
            $newArt[$i]['collect'] = count($collect);
        }
        $collectArt = Db::query(
            'SELECT
                  a.title,
                  a.id,
                  a.cover,
                  a.content,
                  a.issuing_time,
                  u.nick_name,
                COUNT( c.article_id ) AS collectNum 
                FROM
                  article AS a
                LEFT JOIN collect AS c ON a.id = c.article_id
                LEFT JOIN `user` AS u ON a.customer_id = u.id 
                WHERE
                  a.review_status = 1 
                GROUP BY
                  a.id 
                ORDER BY
                collectNum DESC
                LIMIT 5');
        $collectArtList = Db::query(
            'SELECT
                  a.title,
                  a.id,
                  a.cover,
                  a.content,
                  a.issuing_time,
                  u.nick_name,
                COUNT( c.article_id ) AS collectNum 
                FROM
                   article AS a
                LEFT JOIN collect AS c ON a.id = c.article_id
                LEFT JOIN `user` AS u ON a.customer_id = u.id 
                WHERE
                   a.review_status = 1 
                GROUP BY
                   a.id 
                ORDER BY
                   issuing_time DESC,
                   collectNum DESC
                   LIMIT 5');
        $this->assign('newArt',$newArt);
        $this->assign('collectArt',$collectArt);
        $this->assign('collectArtList',$collectArtList);
        return view("index/index");
    }

    public function loginSelect()
    {
        return view("index");
    }

    //跳转注册页
    public function toReg()
    {
        return view('loginReg/register');
    }

    //跳转登录页
    public function toLogin()
    {
        return view('loginReg/login');
    }

    //跳转管理员登录页
    public function adminLogin()
    {
        return view('loginReg/adminLogin');
    }


    //管理员登录
    public function admin(Request $request)
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
        $result = $result[0];
        if ($result) {
            if ($result['password'] == $post['password']) {
                Session::set($result[0]['nick_name'], $result[0]['nick_name']);
                Session::set($result[0]['nick_name'].":id", $result[0]['id']);
                session($result[0]['nick_name'].'loginTime', time());
                //登录成功
                Cookie::set("adminName", $result[0]['nick_name']);
                Cookie::set("adminId", $result[0]['nick_name'].":id");
                Cookie::set("adminTime", $result[0]['nick_name'].'loginTime');
                //登录成功
                return json('success');
            }
        }
        return json("账号密码错误");
    }

    //用户登录
    public function login(Request $request)
    {
        $user = new User();
        $post = $request->post();
        $req = Request::instance();
        /**当登录成功
         * 将用户的nickname存进session
         * 为校验数据进行
         */
        $result = $user->where('nick_Name', $post['nickName'])->select();
        //存在数据
        if ($result[0]) {
            if ($result[0]['pwd'] == $post['password']) {
                Session::set($result[0]['nick_name'], $result[0]['nick_name']);
                Session::set($result[0]['nick_name'].":id", $result[0]['id']);
                session($result[0]['nick_name'].'loginTime', time());
                //登录成功
                Cookie::set("nickname", $result[0]['nick_name']);
                Cookie::set("id", $result[0]['nick_name'].":id");
                Cookie::set("loginTime", $result[0]['nick_name'].'loginTime');
                return json('success');
            }
        }
        return json("账号密码错误");
    }

    //用户注册
    public function reg(Request $request)
    {
        $req = $request->post();
        $user = new User();
        $user->nick_name = $req["nickName"];
        $user->pwd = $req["password"];
        $user->signature = 'noting';
        $result = $user->save();
        if ($result) {
            return json("success");
        } else {
            return json("error");
        }

    }

    //用户退出登录，清除数据
    public function toOut()
    {
        Session::delete(Cookie::get("nickname"));
        Session::delete(Cookie::get("id"));
        Session::delete(Cookie::get("loginTime"));
        Cookie::delete("nickname");
        Cookie::delete("id");
        Cookie::delete("loginTime");
        Cookie::clear();
        $newArt = model("common/Article")->where("review_status", '1')
            ->order('issuing_time desc')->limit(5)->select();
        for ($i = 0; $i < count($newArt); $i++) {
            $customerId = $newArt[$i]->customer_id;
            $articleId = $newArt[$i]->id;
            $newArt[$i]['customer'] = Db::table('user')
                ->where('id', $customerId)->value('nick_name');
            $collect = Db::table('collect')->where('article_id', $articleId)->select();
            $newArt[$i]['collect'] = count($collect);
        }
        $collectArt = Db::query(
            'SELECT
                  a.title,
                  a.id,
                  a.cover,
                  a.content,
                  a.issuing_time,
                  u.nick_name,
                COUNT( c.article_id ) AS collectNum 
                FROM
                  article AS a
                LEFT JOIN collect AS c ON a.id = c.article_id
                LEFT JOIN `user` AS u ON a.customer_id = u.id 
                WHERE
                  a.review_status = 1 
                GROUP BY
                  a.id 
                ORDER BY
                collectNum DESC
                LIMIT 5');
        $collectArtList = Db::query(
            'SELECT
                  a.title,
                  a.id,
                  a.cover,
                  a.content,
                  a.issuing_time,
                  u.nick_name,
                COUNT( c.article_id ) AS collectNum 
                FROM
                   article AS a
                LEFT JOIN collect AS c ON a.id = c.article_id
                LEFT JOIN `user` AS u ON a.customer_id = u.id 
                WHERE
                   a.review_status = 1 
                GROUP BY
                   a.id 
                ORDER BY
                   issuing_time DESC,
                   collectNum DESC
                   LIMIT 5');
        $this->assign('newArt',$newArt);
        $this->assign('collectArt',$collectArt);
        $this->assign('collectArtList',$collectArtList);
        return view("index/index");
    }

}
