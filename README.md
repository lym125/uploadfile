## Laravel UploadFile
`uploadfile` base on [Filesystem / Cloud](https://laravel.com/docs/5.2/filesystem) extras。
## Installation

Require this package with [composer](http://docs.phpcomposer.com/)：
```
composer require lym125/uploadfile
```

## Configuration
1.Find the `providers` key in `config/app.php` and register the UploadFile Service Provider
```php
'providers' => [
    ......

    Lym125\UploadFile\UploadFileServiceProvider::class,
]
```
2.Find the `aliases` key in `config/app.php`
```php
'aliases' => [
    ......

    'UploadFile' => Lym125\UploadFile\Facade\UploadFile::class,
]
```
3.To use your own settings, publish config, `config/captcha.php`.
```
php artisan vendor:publish
```
```php
return [
    'default' => 'local',

    'disks' => [
        'default' => [
            'max_size'      => 0,
            'mime_types'    => ['*'],
            'extensions'    => ['*'],
            'directory'     => 'default',
            'timestamps'    => true,
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
## Usage

```php
$result = UploadFile::handle($file);

if ($result['status'] == 'success') {
    echo 'upload success';
} else {
    echo $result['error'];
}

$configs = [
    'max_size' => 1024*1024*10, # allows max size 10m
    'mime_types' => ['*'], # allows all mime-type
    'extensions' => ['jpg', 'png', 'jpeg'] # allows extensions only jpg,png,jpeg
];

UploadFile::handle($file, $configs);

```