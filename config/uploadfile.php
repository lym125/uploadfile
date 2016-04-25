<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
    */

    'default' => 'local',

    'disks' => [
        'default' => [
            'max_size'      => 0,  // Allows uploading of file size, unit byte. 0 allow all
            'mime_types'    => ['*'], // Allows uploadfile mime-types.
            'extensions'    => ['*'], //  Allows uploadfile extensions.
            'directory'     => 'default', // Allows uploadfile base dir.
            'timestamps'    => true,  //  dir level. {directory}/Y/m/d  
        ],

        'local' => [
            'max_size'      => 1024*1024*10,  // 10Mb。
            'mime_types'    => ['*'],
            'extensions'    => ['*'],
            'directory'     => 'default',
            'timestamps'    => true,
        ]
    ]
];