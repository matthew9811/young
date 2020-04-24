<?php

namespace app\index\controller;

use app\common\model\User;
use app\common\model\Article;
use app\index\controller\common\Base;
use app\index\controller\common\CheckLogin;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;
use think\Response;
use think\Session;
use DateTime;
use app\common\util\JsonUtil;
use app\common\util\CosUtil;

class Person extends CheckLogin
{
    //跳转个人信息修改页
    public function toMine()
    {
        //获取当前用户信息
        $id = Session::get(Cookie::get("id"));
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        return view('person/mine');
    }

    //修改个人资料
    public function setPerson(Request $request)
    {
        $req = $request->post();
        $id = Session::get(Cookie::get("id"));
        $nick_name = $req["nickname"];
        $signature = $req["signature"];
        $pwd = $req["password"];
        if ($req["file"] != '0') {
            $photo = $req["file"];
            $fileKey = str_replace('.', '', uniqid('', true)) . '.html';
            sleep(0.01);
            $cos = new CosUtil();
            $cos->uploadString($fileKey, $photo);
            $result = Db::table("user")->where("id",$id)
                ->setField(["nick_name"=>$nick_name,"signature"=>$signature,"pwd"=>$pwd,"photo"=>$fileKey]);
        } else {
            $result = Db::table("user")->where("id",$id)
                ->setField(["nick_name"=>$nick_name,"signature"=>$signature,"pwd"=>$pwd]);
        }
        if ($result){
            return $this->success("success");
        } else {
            return $this->error("error");
        }

    }

    //跳转他人主页
    public function toOther()
    {
        $cos = new CosUtil();
        //获取当前用户信息
        $id = Session::get(Cookie::get("id"));
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        //获取他人用户信息
        $otherid = input()['id'];
        $other = Base::getUser($otherid)[0];
        $article = Base::getUserArt($otherid);
        for ($i = 0; $i < count($article); $i++) {
            $article[$i]['cover'] = $cos->download($article[$i]['cover']);
        }
        $this->assign('article',$article);
        $this->assign('other',$other);
        return view('person/other');
    }

    //个人收藏列表页
    public function toLikeArtList()
    {
        $cos = new CosUtil();
        $page = input()['page'];
        //获取当前用户的信息
        $userId = Cookie::get("id");
        $id = Session::get($userId);
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        //获取个人收藏文章列表
        $article = $this->getCollectArt($id,$page);
        for ($i = 0; $i < count($article); $i++) {
            $article[$i]['cover'] = $cos->download($article[$i]['cover']);
        }
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        $this->assign('page', $page + 1);
        return view('artList/likeArtList');
    }

    //个人文章列表页
    public function toMineArtList()
    {
        $cos = new CosUtil();
        $page = input()['page']; //获取页码
        //获取当前用户的信息
        $userId = Cookie::get("id");
        $id = Session::get($userId);
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        //获取用户的个人文章列表
        $article = $this->getArt($id,$page);
        for ($i = 0; $i < count($article); $i++) {
            $article[$i]['cover'] = $cos->download($article[$i]['cover']);
        }
        $this->assign("user", $user);
        $this->assign("other", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('page', $page + 1);
        $this->assign('article',$article);
        return view('artList/mineArtList');
    }

    //跳转他人文章页
    public function toOtherArtList()
    {
        $cos = new CosUtil();
        $page = input()['page']; //获取页码
        //获取当前用户的信息
        $userId = Cookie::get("id");
        $id = Session::get($userId);
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        //获取他人id
        $otherId = input()['id'];
        $other = Base::getUser($otherId)[0];
        $article = $this->getArt($otherId,$page);
        for ($i = 0; $i < count($article); $i++) {
            $article[$i]['cover'] = $cos->download($article[$i]['cover']);
        }
        $this->assign("other", $other);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        $this->assign('page', $page + 1);
        return view('artList/otherArtList');
    }

    //获取id对应的个人文章列表
    protected function getArt($id,$page)
    {;
        $article = model("common/Article")->where("customer_id", $id)
            ->where("review_status",'1')->order('issuing_time desc')
            ->limit($page*12,12)->select();
        return $article;
    }

    //获取id对应用户收藏的文章列表
    protected function getCollectArt($id,$page)
    {
        $article = DB::table("collect")->alias('c')
            ->join([['article a', 'c.article_id = a.id']])->where("c.customer_id", $id)
            ->field(['a.id,a.title,a.content,a.issuing_time,a.cover'])
            ->order('issuing_time desc')->limit($page*12,12)
            ->select();
        return $article;
    }

}