<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace bigDream\thinkFilesystemDriver;

use Aws\S3\S3Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use think\filesystem\Driver;

class Ctyun extends Driver
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        // 可见性
        'visibility' => '',
        // 磁盘路径对应的外部URL路径
        'url'        => '',
        // 密钥accessKey
        'key'        => '',
        // 密钥secretAccessKey
        'secret'     => '',
        // 地域节点
        'endpoint'   => '',
        // 储存桶
        'bucket'     => '',
    ];

    protected function createAdapter(): AdapterInterface
    {
        $client = S3Client::factory(array(
            'key'      => $this->config['key'],
            'secret'   => $this->config['secret'],
            'endpoint' => $this->config['endpoint'],
        ));

        return new AwsS3Adapter($client, $this->config['bucket'], '');
    }
}
