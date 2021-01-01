# ThinkPHP 文件系统驱动库

内含以下文件系统驱动

* [天翼云对象储存(融合版)](#ctyun)

## 安装
```
composer require big-dream/think-filesystem-driver
```

## 配置
编辑`config/filesystem.php`文件，在该文件里增加一项磁盘配置信息。
```php
<?php

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

        /** 以下为新增的磁盘配置信息 **/
        'ctyun' => [
            // 磁盘类型
            'type' => \bigDream\thinkFilesystemDriver\Ctyun::class,
            // 可见性（public=公共读，private=私有读）
            'visibility' => 'public',
            // 磁盘路径对应的外部URL路径（Bucket域名）
            'url'      => 'https://test.gdoss.xstore.ctyun.cn',
            // 密钥accessKey（在密钥管理里获取https://console.xstore.ctyun.cn/#/media_key_manage）
            'key'      => 'BUkZ48qgVPSJ3GgWAgDX',
            // 密钥secretAccessKey（在密钥管理里获取https://console.xstore.ctyun.cn/#/media_key_manage）
            'secret'   => 'izPElxxjCkmj7dIwrbOiFjhKyhjO5qwbvqdEqMon',
            // 地域节点（在Bucket的基础配置里获取）
            'endpoint' => 'http://gdoss.xstore.ctyun.cn',
            // 储存桶（Bucket名称）
            'bucket'   => 'test',
        ],
        /** 以上为新增的磁盘配置信息 **/

        // 更多的磁盘配置信息
    ],
];

```

## 使用
### 示例控制器代码
app/controller/Index.php：
```php
<?php
namespace app\controller;

use app\BaseController;
use think\facade\Filesystem;
use think\facade\Request;

class Index extends BaseController
{
    public function index()
    {
        // 如果是GET请求则显示上传表单界面
        if (Request::isGet()) {
            return view();
        }

        try {
            // 获取上传的文件，如果有上传错误，会抛出异常
            $file = \think\facade\Request::file('file');
            // 如果上传的文件为null，手动抛出一个异常，统一处理异常
            if (null === $file) {
                // 异常代码使用UPLOAD_ERR_NO_FILE常量，方便需要进一步处理异常时使用
                throw new \Exception('请上传文件', UPLOAD_ERR_NO_FILE);
            }
            // 保存路径
            $dir = 'avatar';
            $path = Filesystem::disk('ctyun')->putFile($dir, $file);
            // 拼接URL路径
            $url = \think\facade\Filesystem::getDiskConfig('ctyun', 'url') . '/' . str_replace('\\', '/', $path);
        } catch (\Exception $e) {
            // 如果上传时有异常，会执行这里的代码，可以在这里处理异常
            return json([
                'code' => 1,
                'msg'  => $e->getMessage(),
            ]);
        }

        $info = [
            // 文件路径：avatar/a4/e7b9e4ce42e2097b0df2feb8832d28.jpg
            'path' => $path,
            // URL路径：/storage/avatar/a4/e7b9e4ce42e2097b0df2feb8832d28.jpg
            'url'  => $url,
            // 文件大小（字节）
            'size' => $file->getSize(),
            // 文件名：读书顶个鸟用.jpg
            'name' => $file->getOriginalName(),
            // 文件MINE：image/jpeg
            'mime' => $file->getMime(),
        ];
        halt($info);
    }
}
```

### 示例模板文件
view/index/index.html：
```html
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>测试上传</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file"><br/>
    <button type="submit">提交</button>
</form>
</body>
</html>
```