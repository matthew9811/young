<?php
/**
 * Created by PhpStorm.
 * User: chuling
 * Date: 2020/3/21
 * Time: 23:11
 */

namespace app\index\controller;


use think\Controller;
use think\Db;

class Article extends Controller
{
    public function toArticle() {
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


}