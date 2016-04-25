<?php

namespace Lym125\UploadFile\Facade;

use Illuminate\Support\Facades\Facade;

class UploadFile extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'uploadfile';
    }
}