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

    public function toArtList()
    {
        $userId = Cookie::get("id");
        $id = Session::get($userId);
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

    public function toMine()
    {
        $userId = Cookie::get("id");
        $id = Session::get($userId);
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
        $userId = Cookie::get("id");
        $id = Session::get($userId);
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
        $id = input()['id'];
        $user = Base::getUser($id);
        $user = $user[0];
        $article = Base::getUserArt($id);
        $this->assign('article',$article);
        $this->assign('user',$user);
        return view('person/other');
    }

    //个人收藏列表页
    public function toLikeArtList()
    {
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $article = $this->getCollectArt($id);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        return view('artList/likeArtList');
    }

    //个人文章列表页
    public function toMineArtList()
    {
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $article = $this->getArt($id);
        $this->assign("user", $user);
        $this->assign("other", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        return view('artList/mineArtList');
    }

    //跳转他人文章页
    public function toOtherArtList()
    {
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $otherId = input()['id'];
        $other = Base::getUser($otherId)[0];
        $article = $this->getArt($otherId);
        $this->assign("other", $other);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        return view('artList/mineArtList');
    }

    //获取用户个人文章
    protected function getArt($id)
    {;
        $article = model("common/Article")->where("customer_id", $id)
            ->where("review_status",'1')->order('issuing_time desc')
            ->limit(12)->select();
        return $article;
    }

    //获取用户收藏文章
    protected function getCollectArt($id)
    {
        $article = DB::table("collect")->alias('c')
            ->join([['article a', 'c.article_id = a.id']])->where("c.customer_id", $id)
            ->field(['a.id,a.title,a.content,a.issuing_time'])
            ->order('issuing_time desc')->limit(12)
            ->select();
        return $article;
    }

}