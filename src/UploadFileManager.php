<?php

namespace Lym125\UploadFile;

use Illuminate\Http\UploadedFile;

class UploadFileManager
{
    /**
     * 应用实例
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * UploadedFile 实例数组
     *
     * @var \Illuminate\Http\UploadedFile[]
     */
    protected $disks = [];

    /**
     * 创建一个新的 UploadFileManager 实例
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * UploadFile 实例
     *
     * @param  string $name
     * @return \Lym125\UploadFile\UploadFile
     */
    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * UploadFile 实例
     *
     * @param  string $name
     * @return \Lym125\UploadFile\UploadFile
     */
    public function get($name) {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }

    /**
     * 创建一个 UploadFile 实例
     *
     * @param  string $name
     * @return \Lym125\UploadFile\UploadFile
     */
    protected function resolve($name)
    { 
        return new UploadFile($this->app['filesystem']->disk($name), $this->getConfig($name));
    }

    /**
     * Default Filesystem Disk
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->app['config']['uploadfile.default'];
    }

    /**
     * 文件处理规则
     *
     * @param  string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return ($this->app['config']["uploadfile.disks.{$name}"] ?: $this->getDefaultConfig());
    }

    /**
     * 上传文件默认处理规则
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return $this->app['config']["uploadfile.disks.default"];
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->disk(), $method], $parameters);
    }
}