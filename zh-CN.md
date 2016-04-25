## Laravel UploadFile
`uploadfile` 基于[Filesystem / Cloud](https://laravel.com/docs/5.2/filesystem)驱动或其扩展。
## 安装

使用 [Composer](http://docs.phpcomposer.com/) 安装插件：
```
composer require lym125/uploadfile
```

## 配置
1.在应用配置文件 `config/app.php` 中找到 `providers`，注册 `UploadFileServiceProvider`。
```php
'providers' => [
    ......

    Lym125\UploadFile\UploadFileServiceProvider::class,
]
```
2.在应用配置文件 `config/app.php` 中找到 `aliases`。
```php
'aliases' => [
    ......

    'UploadFile' => Lym125\UploadFile\Facade\UploadFile::class,
]
```
3.生成自定义配置文件 `config/uploadfile.php`
```
php artisan vendor:publish
```
```php
return [
    'default' => 'local',

    'disks' => [
        'default' => [
            'max_size'      => 0,  // 允许上传的文件大小，单位字节。0 不限制。
            'mime_types'    => ['*'], // 允许上传的文件媒体类型。默认不限制
            'extensions'    => ['*'], // 允许上传的文件后缀扩展。默认不限制
            'directory'     => 'default', // 上传文件最外层目录
            'timestamps'    => true,  //  最外层目录下，按 年/月/日 生成上传目录文件结构 
        ],
        'local' => [
            'max_size' => 1024*1024*10, //10MB
            'mime_types' => ['*'],
            'extensions' => ['*'],
            'directory' => 'default',
            'timestamps' => true,
        ],
        ......
    ]
];
```
## 使用

```php
$result = UploadFile::handle($file);

if ($result['status'] == 'success') {
    echo '上传成功';
} else {
    echo $result['error'];
}

$configs = [
    'max_size' => 1024*1024*10, # 上传文件最大 10 M
    'mime_types' => ['*'], # 允许上传的文件的媒体类型
    'extensions' => ['jpg', 'png', 'jpeg'] # 允许上传的文件的后缀
];

UploadFile::handle($file, $configs);

```