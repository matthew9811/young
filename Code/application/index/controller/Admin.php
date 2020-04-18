<?php

namespace app\index\controller;


use app\common\model\Manger;
use app\index\controller\common\CheckLogin;
use think\Db;
use think\Request;
use think\Session;

class Admin extends CheckLogin
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
        $result = $result[0];
        if ($result) {
            if ($result['password'] == $post['password']) {
                Session::set("adminNickName", $result['nick_name']);
                Session::set("adminId", $result['id']);
                session('loginTime', time());
                //登录成功
                return json('success');
            }
        }
        return json("账号密码错误");
    }

    //获取该审核文章列表
    public function toAuditList()
    {
        $audit = Db::table('article')->where('review_status','2')
            ->order('issuing_time desc')->select();
        $this->assign('audit',$audit);
        return view('audit/audit');
    }

    //获取审核文章详情
    public function toAuditArt() {
        $id = input()['id'];
        $article = Db::table('article')->where('id',$id)->select();
        $article = $article[0];
        $customerId = $article['customer_id'];
        $customer = Db::table('user')
            ->where('id',$customerId)->select();
        $this->assign('article',$article);
        $this->assign('customer',$customer[0]);
        return view('audit/auditArt');
    }

    //通过该文章审核
    public function passArt()
    {
        $id = input()['id'];
        $reviewer = Session::get("adminId");
        $result = Db::table("article")->where("id",$id)
            ->setField(["review_status"=>'1',"reviewer"=>$reviewer]);
        if ($result){
            return $this->success("success",'/index/Admin/toAuditList');
        } else {
            return $this->error("error");
        }
    }

    //驳回该文章
    public function rejectArt()
    {
        $id = input()['id'];
        $reviewer = Session::get("adminId");
        $result = Db::table("article")->where("id",$id)
            ->setField(["review_status"=>'0',"reviewer"=>$reviewer]);
        if ($result){
            return $this->success("success",'/index/Admin/toAuditList');
        } else {
            return $this->error("error");
        }
    }

    //通过审核选中文章
    public function passList(Request $request)
    {
        $req = $request->post();
        $list = $req['listId'];
        for ($i = 0; $i < count($list);$i++) {
            Db::table('article')->where('id',$list[$i])
                ->setField(['review_status' => '1',"reviewer"=>Session::get("adminId")]);
        }
        return json('success');
    }

    //驳回审核选中文章
    public function rejectList(Request $request)
    {
        $req = $request->post();
        $list = $req['listId'];
        for ($i = 0; $i < count($list);$i++) {
            Db::table('article')->where('id',$list[$i])
                ->setField(['review_status' => '0',"reviewer"=>Session::get("adminId")]);
        }
        return json('success');
    }
}