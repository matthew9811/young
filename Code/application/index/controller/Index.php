<?php

namespace app\index\controller;

use app\common\model\User;
use app\index\controller\common\Base;
use think\Cookie;
use think\Db;
use think\Request;
use think\Response;
use think\Session;
use app\common\util\CosUtil;
use app\common\model\Article;

class Index extends Base
{
    public function index()
    {
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
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
        $this->assign('user',$user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('newArt',$newArt);
        $this->assign('collectArt',$collectArt);
        $this->assign('collectArtList',$collectArtList);
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
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $article = Db::table('article')->where('review_status','1')
            ->order('issuing_time desc')->limit(12)->select();
        $this->assign('article',$article);
        $this->assign('user',$user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        return view('artList/artList');
    }

    public function toAddition()
    {
        Base::_initialize();
        return view('addition/addition');
    }

    public function toMine()
    {
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        return view('person/mine');
    }

    public function adminLogin()
    {
        return view('loginReg/adminLogin');
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

    //用户退出登录
    public function toOut()
    {
        Cookie::delete("nickname");
        Cookie::delete("id");
        Cookie::delete("loginTime");
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
        $article->issuing_time = date('Y-m-d H:i:s', time());
        $article->review_status = '2';
        $result = $article->save();
        if ($result) {
            return $this->success('success');
        } else {
            return $this->error("error");
        }
    }
}
