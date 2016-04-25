<?php

namespace Lym125\UploadFile;

use Storage;
use Illuminate\Support\ServiceProvider;

class UploadFileServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->getDefaultConfigPath() => $this->getConfigPath()
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getDefaultConfigPath(), 
            'uploadfile'
        );

        $this->app->singleton('uploadfile', function($app) {
            return new UploadFileManager($app);
        });
    }

    /**
     * 获取配置文件路径
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('uploadfile.php');
    }

    /**
     * 获取默认配置文件路径
     *
     * @return string
     */
    protected function getDefaultConfigPath()
    {
        return __DIR__ . '/../config/uploadfile.php';
    }
}
