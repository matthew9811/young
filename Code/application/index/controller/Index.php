<?php

namespace app\index\controller;

use app\common\model\User;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use DateTime;
use app\common\util\AES;
use think\View;

class Index extends Controller
{
    public function index()
    {
        return view("index/index");
    }

    public function loginSelect()
    {
        return view("index");
    }

    public function toReg()
    {
        return view('loginReg/register');
    }

    public function toLogin()
    {

        return view('loginReg/login');
    }

    public function toArtList()
    {
        return view('artList/artList');
    }

    public function toAddition()
    {
        return view('addition/addition');
    }

    public function toMine()
    {
        return view('person/mine');
    }

    public function adminLogin()
    {
        return view('loginReg/adminLogin');
    }

    public function toLikeArtList()
    {
        return view('artList/likeArtList');
    }

    public function toMineArtList()
    {
        return view('artList/mineArtList');
    }

    public function toOther()
    {
        return view('person/other');
    }

    public function toAbout()
    {
        $blogs = Db::table('blog')->where('type', '0')
            ->where('status', '1')->where('delete_flag', '0')
            ->limit(3)->select();
        $course = Db::table('blog')->where('type', '1')
            ->where('status', '1')->where('delete_flag', '0')
            ->limit(4)->select();
        $hot = Db::table('blog')->where('delete_flag', '0')
            ->limit(5)->select();
        for ($i = 0; $i < count($blogs); $i = $i + 1) {
            $blog = $blogs[$i];
            $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
            if ($content) {
                $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
                $blog['content'] = $content;
            }
            $blogs[$i] = $blog;
        }
        for ($i = 0; $i < count($course); $i = $i + 1) {
            $blog = $course[$i];
            $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
            if ($content) {
                $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
                $blog['content'] = $content;
            }
            $course[$i] = $blog;
        }
        $this->assign("blog", $blogs);
        $this->assign("course1", $course[0]);
        $this->assign("course2", $course[1]);
        $this->assign("course3", $course[2]);
        $this->assign("course4", $course[3]);
        $this->assign("hot", $hot);
        return view('index/home');
    }

    public function toBlog()
    {
        $id = input()['id'];
        $blog = Db::table('blog')->where('id', $id)->select()[0];
        $user = Db::table('user')->where('id', $blog['user_id'])->select()[0];
        $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
        if ($content) {
            $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
            $blog['content'] = $content;
        }
        if ($blog['label_id'] == 1) {
            $blog['label_id'] = 'photo';
        } else if ($blog['label_id'] == 2) {
            $blog['label_id'] = 'camera';
        } else if ($blog['label_id'] == 3) {
            $blog['label_id'] = 'color';
        } else if ($blog['label_id'] == 4) {
            $blog['label_id'] = 'light';
        } else {
            $blog['label_id'] = '';
        }
        $comment = Db::table('comment')->where('type', '1')
            ->where('type_id', $blog['id'])->select();
        for ($i = 0; $i < count($comment); $i = $i + 1) {
            $userId = $comment[$i];
            $commentor = Db::table('user')->where('id', $userId['user_id'])
                ->select()[0];
            $userId['img'] = $commentor['img'];
            $comment[$i] = $userId;
        }
        $this->assign('user', $user);
        $this->assign('blog', $blog);
        $this->assign('comment', $comment);
        return view('blog/blog');
    }

    public function Content()
    {
        $blogs = Db::table('blog')->where('type', '0')
            ->where('status', '1')->where('delete_flag', '0')
            ->limit(3)->select();
        $course = Db::table('blog')->where('type', '1')
            ->where('status', '1')->where('delete_flag', '0')
            ->limit(4)->select();
        $hot = Db::table('blog')->where('delete_flag', '0')
            ->limit(5)->select();
        for ($i = 0; $i < count($blogs); $i = $i + 1) {
            $blog = $blogs[$i];
            $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
            if ($content) {
                $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
                $blog['content'] = $content;
            }
            $blogs[$i] = $blog;
        }
        for ($i = 0; $i < count($course); $i = $i + 1) {
            $blog = $course[$i];
            $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
            if ($content) {
                $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
                $blog['content'] = $content;
            }
            $course[$i] = $blog;
        }
        $this->assign("blog", $blogs);
        $this->assign("course1", $course[0]);
        $this->assign("course2", $course[1]);
        $this->assign("course3", $course[2]);
        $this->assign("course4", $course[3]);
        $this->assign("hot", $hot);
        return view('index/login_content');

    }

    public function toHome()
    {
        $blogs = Db::table('blog')->where('type', '0')
            ->where('status', '1')->where('delete_flag', '0')
            ->limit(3)->select();
        $course = Db::table('blog')->where('type', '1')
            ->where('status', '1')->where('delete_flag', '0')
            ->limit(4)->select();
        $hot = Db::table('blog')->where('delete_flag', '0')
            ->limit(5)->select();
        for ($i = 0; $i < count($blogs); $i = $i + 1) {
            $blog = $blogs[$i];
            $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
            if ($content) {
                $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
                $blog['content'] = $content;
            }
            $blogs[$i] = $blog;
        }
        for ($i = 0; $i < count($course); $i = $i + 1) {
            $blog = $course[$i];
            $content = fopen(iconv("UTF-8", "gbk", $blog['content']), "r");
            if ($content) {
                $content = file_get_contents(iconv("UTF-8", "gbk", $blog['content']));
                $blog['content'] = $content;
            }
            $course[$i] = $blog;
        }
        $this->assign("blog", $blogs);
        $this->assign("course1", $course[0]);
        $this->assign("course2", $course[1]);
        $this->assign("course3", $course[2]);
        $this->assign("course4", $course[3]);
        $this->assign("hot", $hot);
        return view('index/home');
    }

    //用户登录
    public function login(Request $request)
    {
        $user = new User();
        $post = $request->post();
        $req = Request::instance();
        /**当登录成功
         * 将用户的nickname存进session
         * 为校验数据进行
         */
        $result = $user->where('nick_Name', $post['nickName'])->select();
        //存在数据
        if ($result[0]) {
            if ($result[0]['pwd'] == $post['password']) {
                Session::set("nickName", $result[0]['nick_name']);
                Session::set("id", $result[0]['id']);
                session('loginTime', time());
                //登录成功
                return json('success');
            }
        }
        return json("账号密码错误");
    }

    //用户注册
    public function reg(Request $request)
    {
        $req = $request->post();
        $user = new User();
        $user->nick_name = $req["nickName"];
        $user->pwd = $req["password"];
        $user->signature = 'noting';
        $result = $user->save();
        if ($result) {
            return json('success');
        } else {
            return json("error");
        }

    }

    //用户退出登录
    public function toOut()
    {
        session(null);
        return view("index/index");
    }



}
