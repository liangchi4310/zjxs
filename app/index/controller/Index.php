<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\facade\Event;
use think\facade\Session;

class Index extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
//        url('index/login', ['url' => ""]);
        Event::trigger('admin_nologin', $this);
        $url = Session::get('referer');
        $url = $url ? $url : $this->request->url();
//        $this->error(__('Please login first'), url('index/index', ['url' => $url]));
        return $this -> fetch();
    }

    public function news()
    {
        $newslist = [];

        return jsonp(['newslist' => $newslist, 'new' => count($newslist), 'url' => 'https://www.iuok.cn?ref=news']);
    }
}
