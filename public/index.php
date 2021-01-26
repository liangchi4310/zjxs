<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
// 判断是php 版本
if(version_compare(PHP_VERSION,'7.0.0', '<')){
    header("status: 403 Not Found");exit;
    echo '当前版本为'.phpversion().'小于7.0.0';exit;
}
//是否composer
if (! file_exists('../vendor')) {
    exit('根目录缺少vendor,请先composer install');
}

require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);

