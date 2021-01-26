<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/14
 * Time: 15:28
 */
namespace app\admin\controller;

use think\facade\Db;
use think\facade\Lang;
//use think\AddonService;
use think\facade\Cache;
use think\facade\Config;
use app\common\model\Attachment;
use app\common\controller\Backend;
use fast\Random;
use OSS\OssClient;
use OSS\Core\OssException;
class Ajax extends Backend{
    protected $noNeedLogin = ['lang'];
    protected $noNeedRight = ['*'];
    protected $layout = '';
    public function initialize(){
        parent::initialize();
        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
    }

    /**
     * 加载语言包.
     */
    public function lang(){
        header('Content-Type: application/javascript');
        $controllername = input('controllername');
        //默认只加载了控制器对应的语言名，你还根据控制器名来加载额外的语言包
        $this->loadlang($controllername);
        return jsonp(Lang::get(), 200, [], ['json_encode_param' => JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE]);
    }



    /**
     * 上传文件.
     * @param string $file 上传的文件
     * @return json
     * @author niu
     */
    public function upload(){
        //Config::set('default_return_type', 'json');
        Config::set(['default_return_type'=> 'json'], 'app');
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error(__('No file upload or server upload limit exceeded'));
        }
        //判断是否已经存在附件
        $sha1 = $file->hash();
        //查询
        $attinfo = Db::name('attachment') -> where('sha1',$sha1) -> find();
        if($attinfo){
            $this->success(__('Upload successful'), null, [
                'url' => $attinfo['url'],
            ]);
        }

        $extparam = $this->request->post();
        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int) $upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);

        $fileInfo['name'] = $file->getOriginalName(); //上传文件名
        $fileInfo['type'] = $file->getOriginalMime(); //上传文件类型信息
        $fileInfo['tmp_name'] = $file->getPathname();
        $fileInfo['size'] = $file->getSize();

        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix && preg_match('/^[a-zA-Z0-9]+$/', $suffix) ? $suffix : 'file';

        $mimetypeArr = explode(',', strtolower($upload['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //禁止上传PHP和HTML文件
        if (in_array($fileInfo['type'], ['text/x-php', 'text/html']) || in_array($suffix, ['php', 'html', 'htm'])) {
            $this->error(__('Uploaded file format is limited'));
        }
        //验证文件后缀
        if ($upload['mimetype'] !== '*' &&
            (
                ! in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0].'/', $upload['mimetype']) !== false && (! in_array($fileInfo['type'],
                            $mimetypeArr) && ! in_array($typeArr[0].'/*', $mimetypeArr)))
            )
        ) {
            $this->error(__('Uploaded file format is limited'));
        }
        //验证是否为图片文件
        $imagewidth = $imageheight = 0;
        if (in_array($fileInfo['type'],
                ['image/gif', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/webp']) || in_array($suffix,
                ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'webp'])) {
            $imgInfo = getimagesize($fileInfo['tmp_name']);
            if (! $imgInfo || ! isset($imgInfo[0]) || ! isset($imgInfo[1])) {
                $this->error(__('Uploaded file is not a valid image'));
            }
            $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
            $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
        }

        //上传图片
        $validate = "filesize:{$size}|fileExt:jpg,png,gif,jpeg,bmp,webp";
        $savename = false;

        try {
            $savename = upload_file($file, 'public', 'uploads', $validate);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        if (! $savename) {
            $this->error('上传失败');
        }

        $params = [
            'admin_id'    => (int) $this->auth->id,
            'user_id'     => 0,
            'filesize'    => $fileInfo['size'],
            'imagewidth'  => $imagewidth,
            'imageheight' => $imageheight,
            'imagetype'   => $suffix,
            'imageframes' => 0,
            'mimetype'    => $fileInfo['type'],
            'url'         => $savename,//'uploadtime'  => time(),
            'uploadtime'  => time(),
            'storage'     => 'local',
            'sha1'        => $sha1,
            'extparam'    => json_encode($extparam),
        ];

        $attachment = new Attachment();
        $attachment->data(array_filter($params));
        $attachment->save();
        \think\facade\Event::listen('upload_after', $attachment);
        $this->success(__('Upload successful'), null, [
            'url' => $savename,
        ]);
    }

    public function alyuploadFile(){
        $files = $_FILES['file'];
        $name = $files['name'];
        $format = strrchr($name, '.');//截取文件后缀名如 (.jpg)
        // 设置文件名称。
        //这里是由sha1加密生成文件名 之后连接上文件后缀，生成文件规则根据自己喜好，也可以用md5
        //前面video/head/ 这是我的oss目录
        $object = 'video/head/'.sha1(date('YmdHis', time()) . uniqid()) . $format;;
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        $filePath = $files['tmp_name'];
        try{
            $accessKeyId = config('aliyun_oss.accessKeyId');//去阿里云后台获取秘钥
            $accessKeySecret = config('aliyun_oss.accessKeySecret');//去阿里云后台获取秘钥
            $endpoint = config('aliyun_oss.endpoint');//你的阿里云OSS地址
            $bucket = config('aliyun_oss.bucket');//你的阿里云OSS地址
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $result = $ossClient->uploadFile($bucket, $object, $filePath);
            if(!$result){
                return json(['status'=>1,'message'=>'上传失败']);
            }else{
                return json(['status'=>2,'message'=>'上传成功','url'=>$result['info']['url']]);
            }
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");

//        $file= request()->file('icon');
//        $resResult = Image::open($file);
//        $type = $resResult->type();
//        // 尝试执行
//        try {
//            $accessKeyId = config('aliyun_oss.accessKeyId');//去阿里云后台获取秘钥
//            $accessKeySecret = config('aliyun_oss.accessKeySecret');//去阿里云后台获取秘钥
//            $endpoint = config('aliyun_oss.endpoint');//你的阿里云OSS地址
//            $bucket = config('aliyun_oss.bucket');//你的阿里云OSS地址
//            //实例化对象 将配置传入
//            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
//            //这里是有sha1加密 生成文件名 之后连接上后缀
//            $fileName = sha1(date('YmdHis', time()) . uniqid()) . '.' . $type;
//            //上传至阿里云的目录 为年+月/日的格式
//            $pathName = date('Y-m/d') . '/' .$fileName;
//            //执行阿里云上传 bucket名称,上传的目录,文件
//            $result = $ossClient->uploadFile($bucket, $pathName, $file->getInfo()['tmp_name']);
//        } catch (OssException $e) {
//            return $e->getMessage();
//        }
        //将结果输出
        return $result['info']['url'];
    }
    public function index(){
        //获取上传文件
        $image = $this->request->file('image');
        //获取上传后的文件路径
        $qiniu_file = \think\facade\Filesystem::disk('qiniu')->putFile('image',$image);
        dd($qiniu_file);
    }
    /**
     * 上传图片
     * @param $request
     */
    public static function qiUpload($request)
    {
        //    use think\facade\Config;
        //    use Qiniu\Auth;
        //    use Qiniu\Storage\UploadManager;

        if($request->isPost()){
            $img = QiUpload::qiUpload($request);
            if($img){
                // 图片完整绝对路经
                $imgUrl = Config::get('qiniu.image_url') . '/' . $img;
                $data = [
                    'status'    =>  1,
                    'msg'       =>  '上传成功',
                    'img_url'   =>  $imgUrl,
                ];
                return json($data);
            }else{
                return json(['status'=>0,'msg'=>'上传失败']);
            }
        }

        // 获取上传图片信息
        $file = $request->file('file');
        // 图片存储在本地的临时路经
        $filePath = $file->getRealPath();
        // 获取图片后缀
        $ext = $file->getOriginalExtension();
        // 上传到七牛后保存的新图片名
        $newImageName  = date('Y') . '/' . date('m') .'/' . substr(md5($file->getOriginalName()),0,6)
            . date('YmdHis') . rand(00000,99999) . '.'.$ext;
        // 说明：为了方便阅读，上一行代码进行的回车，如果你的遇到了问题，请放到一行


        // 构建鉴权对象
        $auth = new Auth(Config::get('qiniu.ak') , Config::get('qiniu.sk'));
        // 要上传的空间位置
        $token = $auth->uploadToken(Config::get('qiniu.bucket'));

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        list($ret , $err) = $uploadMgr->putFile($token , $newImageName , $filePath);
        if($err !== null){
            return null;
        }else{
            // 图片上传成功
            return $newImageName;
        }

    }
    /**
     * 通用排序.
     */
    public function weigh(){
        //排序的数组
        $ids = $this->request->post('ids');
        //拖动的记录ID
        $changeid = $this->request->post('changeid');
        //操作字段
        $field = $this->request->post('field');
        //操作的数据表
        $table = $this->request->post('table');
        //主键
        $pk = $this->request->post('pk');
        //排序的方式
        $orderway = $this->request->post('orderway', '', 'strtolower');
        $orderway = $orderway == 'asc' ? 'ASC' : 'DESC';
        $sour = $weighdata = [];
        $ids = explode(',', $ids);
        $prikey = $pk ? $pk : (Db::name($table)->getPk() ?: 'id');
        $pid = $this->request->post('pid');
        //限制更新的字段
        $field = in_array($field, ['weigh']) ? $field : 'weigh';

        // 如果设定了pid的值,此时只匹配满足条件的ID,其它忽略
        if ($pid !== '') {
            $hasids = [];
            $list = Db::name($table)->where($prikey, 'in', $ids)->where('pid', 'in',
                $pid)->field("{$prikey},pid")->select();
            foreach ($list as $k => $v) {
                $hasids[] = $v[$prikey];
            }
            $ids = array_values(array_intersect($ids, $hasids));
        }

        $list = Db::name($table)->field("$prikey,$field")->where($prikey, 'in', $ids)->order($field,
            $orderway)->select();
        foreach ($list as $k => $v) {
            $sour[] = $v[$prikey];
            $weighdata[$v[$prikey]] = $v[$field];
        }
        $position = array_search($changeid, $ids);
        $desc_id = $sour[$position];    //移动到目标的ID值,取出所处改变前位置的值
        $sour_id = $changeid;
        $weighids = [];
        $temp = array_values(array_diff_assoc($ids, $sour));
        foreach ($temp as $m => $n) {
            if ($n == $sour_id) {
                $offset = $desc_id;
            } else {
                if ($sour_id == $temp[0]) {
                    $offset = isset($temp[$m + 1]) ? $temp[$m + 1] : $sour_id;
                } else {
                    $offset = isset($temp[$m - 1]) ? $temp[$m - 1] : $sour_id;
                }
            }
            $weighids[$n] = $weighdata[$offset];
            Db::name($table)->where($prikey, $n)->update([$field => $weighdata[$offset]]);
        }
        $this->success();
    }

    /**
     * 清空系统缓存.
     */
    /**
     * 清空系统缓存.
     */
    public function wipecache()
    {
        $type = $this->request->request('type');
        switch ($type) {
            case 'all':
            case 'content':
                rmdirs(app()->getRootPath().'runtime'.DIRECTORY_SEPARATOR, false);
                Cache::clear();
                if ($type == 'content') {
                    break;
                }
            case 'template':
                rmdirs(app()->getRootPath().'runtime'.DIRECTORY_SEPARATOR, false);
                if ($type == 'template') {
                    break;
                }
//            case 'addons':
//                AddonService::refresh();
//                if ($type == 'addons') {
//                    break;
//                }
        }

        \think\facade\Event::trigger('wipecache_after');
        $this->success();
    }

   
    public function goodcategory(){
        $pid = $this->request->get('pid/d');
        $where = "status = 1";
        $categorylist = null;
        if($pid == 0){
            $where.= " and pid=$pid";
            $categorylist = Db::name('category')->where($where)->field('id as value,cate_name as name')->order('id desc')->select();
        }else{
            $where.= " and pid=$pid";
            $categorylist = Db::name('category')->where($where)->field('id as value,cate_name as name')->order('id desc')->select();
        }
        $this->success('', null, $categorylist);
    }

    /**
     * 读取分类数据,联动列表.
     */
    public function category(){
        $type = $this->request->get('type');
        $pid = $this->request->get('pid');
        $where = ['status' => 1];
        $categorylist = null;
        if ($pid !== '') {
            if ($type) {
                $where['type'] = $type;
            }
            if ($pid) {
                $where['pid'] = $pid;
            }

            $categorylist = Db::name('category')->where($where)->field('id as value,name')->order('id desc')->select();

        }
        $this->success('', null, $categorylist);
    }

    /**
     * 读取省市区数据,联动列表.
     */
    public function area(){
        $params = $this->request->get('row/a');
        if (! empty($params)) {
            $province = isset($params['province']) ? $params['province'] : '';
            $city = isset($params['city']) ? $params['city'] : null;
        } else {
            $province = $this->request->get('province');
            $city = $this->request->get('city');
        }
        $where = ['pid' => 0, 'level' => 1];
        $provincelist = null;
        if ($province !== '') {
            if ($province) {
                $where['pid'] = $province;
                $where['level'] = 2;
            }
            if ($city !== '') {
                if ($city) {
                    $where['pid'] = $city;
                    $where['level'] = 3;
                }
                $provincelist = Db::name('area')->where($where)->field('id as value,name')->select();
            }
        }
        $this->success('', null, $provincelist);
    }


}
