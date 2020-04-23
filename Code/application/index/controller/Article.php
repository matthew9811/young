<?php

namespace app\index\controller;


use app\common\model\Collect;
use app\common\util\CosUtil;
use app\common\util\JsonUtil;
use app\index\controller\common\Base;
use app\index\controller\common\CheckLogin;
use think\Db;
use think\Request;
use think\Response;
use think\Session;
use think\Cookie;

class Article extends CheckLogin
{


    public function searchArtList()
    {
        $page = input()['page'];
        //标题
        $searchConditions = input()['conditions'];
        //获取当前用户信息
        $id = Session::get(Cookie::get("id"));
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        //获取文章信息，一页12篇
        $map['review_status'] = array('=', '1');
        $map['title'] = array('like', "%" . $searchConditions . "%");
        $article = Db::table('article')->where($map)
            ->order('issuing_time desc')->limit($page * 12, 12)->select();
        $this->assign("sql", Db::table('article')->getLastSql());
        $this->assign('user', $user);
        $this->assign('userArt', $userArt);
        $this->assign('userCollect', $userCollectArt);
        $this->assign('page', $page + 1);
        $this->assign('article', $article);
        $this->assign("conditions", $searchConditions);
        return view('artList/searchList');
    }

    //跳转日记页，获取文章列表
    public function toArtList()
    {
        $page = input()['page'];
        //获取当前用户信息
        $id = Session::get(Cookie::get("id"));
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        //获取文章信息，一页12篇
        $article = Db::table('article')->where('review_status', '1')
            ->order('issuing_time desc')->limit($page * 12, 12)->select();
        $this->assign('user', $user);
        $this->assign('userArt', $userArt);
        $this->assign('userCollect', $userCollectArt);
        $this->assign('page', $page + 1);
        $this->assign('article', $article);
        return view('artList/artList');
    }

    //获取不同类型的文章列表 1为日期降序排序，2为收藏量与日期降序排序
    public function getArtList()
    {
        $page = input()['page'];
        //获取文章列表
        if (input()['type'] == '1') {
            $article = Db::table('article')->where('review_status', '1')
                ->order('issuing_time desc')->limit($page * 12, 12)->select();
        } else { //提示：下面的sql需要分页
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
        //获取当前用户信息
        $userId = Cookie::get("id");
        $id = Session::get($userId);
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        $this->assign("user", $user);
        $this->assign('userArt', $userArt);
        $this->assign('userCollect', $userCollectArt);
        $this->assign('article', $article);
        $this->assign('page', $page + 1);
        return view('artList/artList');
    }

    //跳转文章分享页
    public function toAddition()
    {
        return view('addition/addition');
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
//        $fileKey = str_replace('.', '', uniqid('', true)) . '.html';
//        sleep(0.01);
//        $contentKey = str_replace('.', '', uniqid('', true)) . '.html';
//        $cos = new CosUtil();
//        $cos->uploadString($contentKey, $content);
//        $article = new \app\common\model\Article();
//        $article->customer_id = Session::get("id");
//        $article->content = $contentKey;
//        $article->cover = $fileKey;
//        $article->title = $title;
//        $article->issuing_time = date('Y-m-d H:i:s', time());
//        $article->review_status = '2';
//        $result = $article->save();
//        if ($result) {
//            return $this->success('success');
//        } else {
//            return $this->error("error");
//        }
        return json(['file', $file]);

    }

    //文章详情页
    public function toArticle()
    {
        //获取当前用户信息
        $id = Session::get(Cookie::get("id"));
        $user = Base::getUser($id)[0];
        $userArt = Base::getUserArt($id);
        $userCollectArt = Base::getUserCollectArt($id);
        //根据跳转的文章id获取文章详情
        $articleId = input()['id'];
        $article = Db::table('article')->where('id', $articleId)->select();
        $article = $article[0];
        //获取文章作者信息
        $customerId = $article['customer_id'];
        $customer = Base::getUser($customerId);
        //获取当前用户是否收藏，收藏为1，没有收藏为2
        $collect = Db::table('collect')->where('article_id', $articleId)
            ->where('customer_id', $id)->select();
        if ($collect) {
            $article['collect'] = 1;
        } else {
            $article['collect'] = 2;
        }
        $this->assign("user", $user);
        $this->assign('userArt', $userArt);
        $this->assign('userCollect', $userCollectArt);
        $this->assign('article', $article);
        $this->assign('customer', $customer[0]);
        return view('article/article');
    }

    //收藏文章
    public function collectArt()
    {
        $collect = new Collect();
        $collect->article_id = input()['id'];//获取文章id
        $collect->customer_id = Session::get(Cookie::get("id"));//获取当前用户id
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
        $article_id = input()['id'];//获取文章id
        $customer_id = Session::get(Cookie::get("id"));//获取当前用户id
        $result = Db::table("collect")->where("article_id", $article_id)
            ->where("customer_id", $customer_id)->delete();
        if ($result) {
            return json('success');
        } else {
            return json('error');
        }
    }

}