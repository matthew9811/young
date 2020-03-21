<?php

namespace app\index\controller;

use app\common\model\User;
use app\common\model\Article;
use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use think\Session;
use DateTime;
use app\common\util\JsonUtil;
use app\common\util\CosUtil;

class Person extends Controller
{

    //修改个人资料
    public function setPerson(Request $request)
    {
        $req = $request->post();
        $id = Session::get("id");
        $nick_name = $req["nickname"];
        $signature = $req["signature"];
        $pwd = $req["password"];
        if ($req["file"] != '0') {
            $photo = $req["file"];
            $fileKey = str_replace('.', '', uniqid('', true)) . '.html';
            sleep(0.01);$cos = new CosUtil();
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
        $user = $this->getUser($id);
        $user = $user[0];
        $article = $this->getUserArt($id);
        $this->assign('article',$article);
        $this->assign('user',$user);
        return view('person/other');
    }

    //获取id对应的用户信息
    protected function getUser($id)
    {
        $user = model("common/User")->where("id", $id)->select();
        return $user;
    }

    //获取用户个人文章
    protected function getUserArt($id) {
        $article = model("common/Article")->where("customer_id", $id)
            ->where("review_status",'1')->select();
        return $article;
    }

}