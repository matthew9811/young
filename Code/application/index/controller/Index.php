<?php

namespace app\index\controller;

use app\common\model\User;
use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use think\Session;
use DateTime;
use app\common\util\JsonUtil;
use app\common\util\CosUtil;
use app\common\model\Article;

class Index extends Controller
{
    public function index()
    {
        return view("index/index");
    }

    public function loginSelect()
    {
        return view("index");
    }

    public function toReg()
    {
        return view('loginReg/register');
    }

    public function toLogin()
    {

        return view('loginReg/login');
    }

    public function toArtList()
    {
        return view('artList/artList');
    }

    public function toAddition()
    {
        $obj=controller("index/common/Base");
        $obj->_initialize();
        return view('addition/addition');
    }

    public function toMine()
    {
        $id = Session::get("id");
        $user = $this->getUser($id);
        $user = $user[0];
        $this->assign("user", $user);
        return view('person/mine');
    }

    protected function getUser($id)
    {
        $user = model("common/User")->where("id", $id)->select();
        return $user;
    }

    public function adminLogin()
    {
        return view('loginReg/adminLogin');
    }

    public function toLikeArtList()
    {
        return view('artList/likeArtList');
    }

    public function toMineArtList()
    {
        return view('artList/mineArtList');
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
                Session::set("nickName", $result[0]['nick_name']);
                Session::set("id", $result[0]['id']);
                session('loginTime', time());
                //登录成功
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

    //用户退出登录
    public function toOut()
    {
        session(null);
        return view("index/index");
    }


    /**
     * 新增日记
     * @param Request $request
     * @param Response $response
     * @return msg
     */
    public function addition(Request $request, Response $response)
    {
        $post = $request->post();
        //标题
        $title = $post['title'];
        //内容
        $content = $post['code'];
        //封面base64
        $file = $post['file'];
        $fileKey = str_replace('.', '', uniqid('', true)) . '.html';
        sleep(0.01);
        $contentKey = str_replace('.', '', uniqid('', true)) . '.html';
        $cos = new CosUtil();
        $cos->uploadString($fileKey, $file);
        $cos->uploadString($contentKey, $content);
        $article = new Article();
        $article->customer_id = Session::get("id");
        $article->content = $contentKey;
        $article->cover = $fileKey;
        $article->title = $title;
        $article->issuing_time = date('Y-m-d H:i:s',time());
        $article->review_status = '2';
        $result = $article->save();
        if ($result) {
            return $this->success('success');
        } else {
            return $this->error("error");
        }
    }

}
