<?php
//上传配置
return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'local'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        //阿里云
        'aliyun' => [
            'type'         => 'aliyun',
            'accessKeyId'      => '<yourAccessKeyId>',  //Access Key ID
            'accessKeySecret'  => '<yourAccessKeySecret>',  //Access Key Secret
            'bucket'     => '<yourBucketName>',  //Bucket名称
            'endpoint '   => 'oss-cn-hongkong.aliyuncs.com',  //阿里云oss 外网地址endpoint
            'url'          => 'http://oss-cn-hongkong.aliyuncs.com',//不要斜杠结尾，此处为URL地址域名。
        ],

        //newly added
        'qiniu'=>[
            'type'=>'qiniu',
            'accessKey'=>'your accessKey',//你的accessKey
            'secretKey'=>'your secretKey',//你的secretKey
            'bucket'=>'your qiniu bucket name',//要上传的空间
            'domain'=>'your qiniu bind domain' //空间域名   不要斜杠结尾，此处为URL地址域名。
        ],
        'qcloud' => [
            'type'       => 'qcloud',
            'region'      => '***', //bucket 所属区域 英文
            'appId'      => '***', // 域名中数字部分
            'secretId'   => '***',
            'secretKey'  => '***',
            'bucket'          => '***',
            'timeout'         => 60,
            'connect_timeout' => 60,
            'cdn'             => '您的 CDN 域名',
            'scheme'          => 'https',
            'read_from_cdn'   => false,
        ],
        // 更多的磁盘配置信息



    ],
];
