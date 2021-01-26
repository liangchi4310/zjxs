<?php
/**
 * *
 *  * ============================================================================
 *  * Created by PhpStorm.
 *  * User: Ice
 *  * 邮箱: ice@sbing.vip
 *  * 网址: https://sbing.vip
 *  * Date: 2019/9/19 下午3:20
 *  * ============================================================================.
 */

namespace app\common\controller;
use think\exception\HttpResponseException;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Validate;
use think\facade\View;
use think\facade\Event;
use think\facade\Config;
use app\common\library\Auth;
use app\BaseController;
use think\facade\View as ViewTemplate;
use think\Response;

/**
 * 前台控制器基类.
 */
class Frontend extends BaseController{
    /**
     * 布局模板
     *
     * @var string
     */
    protected $layout = '';

    /**
     * 无需登录的方法,同时也就不需要鉴权了.
     *
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录.
     *
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 权限Auth.
     *
     * @var Auth
     */
    protected $auth = null;

    public function _initialize()
    {
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
        $modulename = app()->http->getName();
        $controller = preg_replace_callback('/\.[A-Z]/', function ($d) {
            return strtolower($d[0]);
        }, $this->request->controller(), 1);

        $controllername = parseName($controller);
        $actionname = strtolower($this->request->action());

        // 如果有使用模板布局
        if ($this->layout) {
            View::engine()->layout('layout/'.$this->layout);
        }
        $this->auth = app()->auth;

        // token
        $token = $this->request->server('HTTP_TOKEN',
            $this->request->request('token', \think\facade\Cookie::get('token')) ?: '');

        $path = str_replace('.', '/', $controllername).'/'.$actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (! $this->auth->match($this->noNeedLogin)) {
            //初始化
            $this->auth->init($token);
            //检测是否登录
            if (! $this->auth->isLogin()) {
                $this->error(__('Please login first'), 'index/user/login');
            }
            // 判断是否需要验证权限
            if (! $this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (! $this->auth->check($path)) {
                    $this->error(__('You have no permission'));
                }
            }
        } else {
            // 如果有传递token才验证是否登录状态
            if ($token) {
                $this->auth->init($token);
            }
        }

        $this->assign('user', $this->auth->getUser());

        // 语言检测
        $lang = strip_tags(Lang::getLangSet());

        $site = Config::get('site');

        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Event::trigger('upload_config_init', $upload);

        // 配置信息
        $config = [
            'site'           => array_intersect_key($site,array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'upload'         => $upload,
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => 'frontend/'.str_replace('.', '/', $controllername),
            'moduleurl'      => rtrim(url("/{$modulename}", [], false), '/'),
            'language'       => $lang,
        ];

        Config::set(array_merge(Config::get('upload'), $upload), 'upload');
        // 配置信息后
        Event::trigger('config_init', $config);
        // 加载当前控制器语言包
        $this->loadlang($this->request->controller());
        $this->assign('site', $site);
        $this->assign('config', $config);
    }

    /**
     * 加载语言文件.
     *
     * @param string $name
     */
    protected function loadlang($name)
    {
        if (strpos($name, '.')) {
            $_arr = explode('.', $name);
            if (count($_arr) == 2) {
                $path = $_arr[0].'/'.parseName($_arr[1]);
            } else {
                $path = strtolower($name);
            }
        } else {
            $path = parseName($name);
        }
        Lang::load(app()->getAppPath().'/lang/'.Lang::getLangset().'/'.$path.'.php');
    }

    /**
     * 渲染配置信息.
     *
     * @param mixed $name  键名或数组
     * @param mixed $value 值
     */
    protected function assignconfig($name, $value = '')
    {
        $this->view->config = array_merge($this->view->config ? $this->view->config : [],
            is_array($name) ? $name : [$name => $value]);
    }

    /**
     * 刷新Token
     */
    protected function token()
    {
        $token = $this->request->post('__token__');

        //验证Token
        if (!Validate::is($token, "token", ['__token__' => $token])) {
            $this->error(__('Token verification error'), '', ['__token__' => $this->request->buildToken()]);
        }

        //刷新Token
        $this->request->buildToken();
    }

    /**
     * 操作成功跳转的快捷方法.
     *
     * @param mixed  $msg    提示信息
     * @param string $url    跳转的 URL 地址
     * @param mixed  $data   返回的数据
     * @param int    $wait   跳转等待时间
     * @param array  $header 发送的 Header 信息
     *
     * @throws \Exception
     */
    protected function success($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        if (is_null($url) && ! is_null(Request::server('HTTP_REFERER'))) {
            $url = Request::server('HTTP_REFERER');
        } elseif ('' !== $url && ! strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = url($url);
        }

        $type = $this->getResponseType();
        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if ('html' == strtolower($type)) {
            $result = ViewTemplate::fetch(Config::get('app.dispatch_success_tmpl'), $result);
        }

        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法.
     *
     * @param mixed  $msg    提示信息
     * @param string $url    跳转的 URL 地址
     * @param mixed  $data   返回的数据
     * @param int    $wait   跳转等待时间
     * @param array  $header 发送的 Header 信息
     *
     * @throws \Exception
     */
    protected function error($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url = Request::isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && ! strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = url($url);
        }

        $type = $this->getResponseType();
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if ('html' == strtolower($type)) {
            $result = ViewTemplate::fetch(Config::get('app.dispatch_success_tmpl'), $result);
        }
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的 API 数据到客户端.
     *
     * @param mixed  $data   要返回的数据
     * @param int    $code   返回的 code
     * @param mixed  $msg    提示信息
     * @param string $type   返回数据格式
     * @param array  $header 发送的 Header 信息
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::server('REQUEST_TIME'),
            'data' => $data,
        ];
        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型.
     *
     * @return string
     */
    protected function getResponseType(){
        return Request::isAjax()
            ? Config::get('app.default_ajax_return')
            : Config::get('app.default_return_type');
    }
}
