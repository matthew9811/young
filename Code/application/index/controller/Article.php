<?php

namespace app\index\controller;


use app\common\model\Collect;
use app\common\util\CosUtil;
use app\index\controller\common\Base;
use app\index\controller\common\CheckLogin;
use think\Db;
use think\Request;
use think\Response;
use think\Session;

class Article extends CheckLogin
{


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
        $fileKey = str_replace('.', '', uniqid('', true)) . '.html';
        sleep(0.01);
        $contentKey = str_replace('.', '', uniqid('', true)) . '.html';
        $cos = new CosUtil();
        $cos->uploadString($fileKey, $file);
        $cos->uploadString($contentKey, $content);
        $article = new \app\common\model\Article();
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