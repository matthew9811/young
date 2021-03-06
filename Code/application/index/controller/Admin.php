<?php

namespace app\index\controller;


use app\index\controller\common\CheckAdmin;
use think\Db;
use think\Request;
use think\Session;
use think\Cookie;
use app\common\util\CosUtil;

class Admin extends CheckAdmin
{


    //获取该审核文章列表
    public function toAuditList()
    {
        $cos = new CosUtil();
        $page = input()['page'];
        $adminId = Session::get(Cookie::get("adminId"));
        $admin = Db::table('manger')->where("id", $adminId)->select();
        $audit = Db::table('article')->where('review_status','2')
            ->order('issuing_time desc')->limit($page * 12, 12)->select();
        for ($i = 0; $i < count($audit); $i++) {
            $audit[$i]['cover'] = $cos->download($audit[$i]['cover']);
        }
        $this->assign('page', $page + 1);
        $this->assign('admin',$admin[0]);
        $this->assign('audit',$audit);
        return view('audit/audit');
    }

    //获取审核文章详情
    public function toAuditArt()
    {
        $cos = new CosUtil();
        $id = input()['id'];
        $article = Db::table('article')->where('id',$id)->select();
        $article = $article[0];
        $article['cover'] = $cos->download($article['cover']);
        $customerId = $article['customer_id'];
        $customer = Db::table('user')
            ->where('id',$customerId)->select();
        $adminId = Session::get(Cookie::get("adminId"));
        $admin = Db::table('manger')->where("id", $adminId)->select();
        $this->assign('admin',$admin[0]);
        $this->assign('article',$article);
        $this->assign('customer',$customer[0]);
        $this->assign('content', $cos->download($article['content']));
        return view('audit/auditArt');
    }

    //通过该文章审核
    public function passArt()
    {
        $id = input()['id'];
        $reviewer = Session::get(Cookie::get("adminId"));
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
        $reviewer = Session::get(Cookie::get("adminId"));
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
                ->setField(['review_status' => '1',
                    "reviewer"=>Session::get(Cookie::get("adminId"))]);
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
                ->setField(['review_status' => '0',
                    "reviewer"=>Session::get(Cookie::get("adminId"))]);
        }
        return json('success');
    }
}