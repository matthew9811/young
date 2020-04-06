<?php

namespace app\index\controller;


use app\common\model\Collect;
use app\index\controller\common\Base;
use think\Controller;
use think\Db;
use think\Session;

class Article extends Base
{
    //获取不同类型的文章列表 1为日期降序排序，2为收藏量与日期降序排序
    public function getArtList()
    {
        if (input()['type'] == '1')
        {
            $article = Db::table('article')->where('review_status','1')
                ->order('issuing_time desc')->limit(12)->select();
        } else {
            $article = Db::query(
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
                   LIMIT 12');
        }
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        return view('artList/artList');
    }

    //文章详情页
    public function toArticle()
    {
        $id = Session::get("id");
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $articleId = input()['id'];
        $article = Db::table('article')->where('id',$articleId)->select();
        $article = $article[0];
        $customerId = $article['customer_id'];
        $customer = Base::getUser($customerId);
        $collect = Db::table('collect')->where('article_id', $articleId)
            ->where('customer_id', $id)->select();
        if ($collect) {
            $article['collect'] = 1;
        } else {
            $article['collect'] = 2;
        }
        $this->assign("user", $user);
        $this->assign('userArt',$userArt);
        $this->assign('userCollect',$userCollectArt);
        $this->assign('article',$article);
        $this->assign('customer',$customer[0]);
        return view('article/article');
    }

    //收藏文章
    public function collectArt()
    {
        $obj=controller("index/common/Base");
        $obj->_initialize();
        $collect = new Collect();
        $collect->article_id = input()['id'];
        $collect->customer_id = Session::get("id");
        $result = $collect->save();
        if ($result) {
            return json('success');
        } else {
            return json('error');
        }

    }

    //取消收藏
    public function cancelCollectArt()
    {
        $obj=controller("index/common/Base");
        $obj->_initialize();
        $article_id = input()['id'];
        $customer_id = Session::get("id");
        $result = Db::table("collect")->where("article_id",$article_id)
            ->where("customer_id",$customer_id)->delete();
        if ($result) {
            return json('success');
        } else {
            return json('error');
        }
    }

}