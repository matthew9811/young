<?php

namespace app\index\controller\common;

use think\Controller;
use think\Cookie;
use think\Db;
use app\common\util\CosUtil;
use think\Session;

/**
 * 用于书写一些通用的方法
 * Class Base
 * @package app\index\controller
 */
class Base extends Controller
{


    public function _initialize()
    {

        $id = Cookie::get("id");
        parent::_initialize();
        if (!Session::has($id)) {
            Cookie::clear();
        }
    }

    //获取id对应的用户信息
    public function getUser($id)
    {
        $user = model("common/User")->where("id", $id)->select();
        $cos = new CosUtil();
        if ($user[0]['photo'] != '') {
            $user[0]['photo'] = $cos->download($user[0]['photo']);
        } else {
            $user[0]['photo'] = '/common/uploads/2018/05/img.jpg';
        }
        return $user;
    }

    //获取用户个人最新的五篇文章
    public function getUserArt($id)
    {
        ;
        $article = model("common/Article")->where("customer_id", $id)
            ->where("review_status", '1')->order('issuing_time desc')
            ->limit(5)->select();
        return $article;
    }

    //获取用户收藏的最新五篇文章
    public function getUserCollectArt($id)
    {
        $article = DB::table("collect")->alias('c')
            ->join([['article a', 'c.article_id = a.id']])->where("c.customer_id", $id)
            ->field(['a.id,a.title,a.content,a.issuing_time'])
            ->order('issuing_time desc')->limit(5)
            ->select();
        return $article;
    }
}